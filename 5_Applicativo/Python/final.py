import time
import numpy as np
import cv2
import argparse
import imutils
import os
import glob
from datetime import datetime
from os.path import exists
from sys import platform
import bleedfacedetector as fd
from PIL import Image

__author__ = "Gioele Cavallo"
__copyright__ = "Copyright 2007, The Cogent Project"
__version__ = "1.0"
__email__ = "gioele.cavallo@samtrevano.ch"
__status__ = "Production"

NMS_THRESHOLD=0.3
MIN_CONFIDENCE=0.2

def args_parser():
    """
    This function parses parameters passed to the program and return them.
    """

    arg_parse = argparse.ArgumentParser()

    arg_parse.add_argument("-o","--output",default=None,type=str, help="Output file for statistics")
    arg_parse.add_argument("-c","--camera",default=False, help="Set to true for camera")
    arg_parse.add_argument("-v","--video",default=None, help="The video path")

    args = vars(arg_parse.parse_args())
    return args
    
def check_args(args):
    """
    This function return True if arguments (args) are valid, otherwise False.
    """

    if args["camera"] == False and not args["video"]:
        print("Set an input file/video with -c True or -v <path>")
        return False
    elif args["camera"] == True and args["video"] != "":
        print("Set only one input file/video")
        return False
    else:
        return True

def test_input(input_detection):
    """
    This function return the existence of the camera.
    """
    
    if isinstance(input_detection,str):
        if not exists(input_detection):
            print("Given file doesn't exists")
        return exists(input_detection)

    video = cv2.VideoCapture(input_detection, cv2.CAP_DSHOW)
    if video.isOpened():
        return True
    print("Unable to open camera")
    return False
    
def detect_from_image(image, model, layer_name, personidz=0):
    """
    This function return the coordinades of the faces of detected persons from an image (image).
    """
    
    (H, W) = image.shape[:2]


    # Convert image to blob
    blob = cv2.dnn.blobFromImage(image, 1 / 255.0, (416, 416), swapRB=True, crop=False)

    # Set input for detection
    model.setInput(blob)
    layerOutputs = model.forward(layer_name)

    # Array for coordinades
    boxes = []

    # Array for center of coordinades
    centroids = []

    # Array for confidences
    confidences = []

    for output in layerOutputs:
        for detection in output:
            scores = detection[5:]
            
            # Extract the object name
            classID = np.argmax(scores)
            confidence = scores[classID]

            # Take only persons objects with a certain confidence
            if classID == personidz and confidence > MIN_CONFIDENCE:

                box = detection[0:4] * np.array([W, H, W, H])
                (centerX, centerY, width, height) = box.astype("int")

                x = int(centerX - (width / 2))
                y = int(centerY - (height / 2))

                boxes.append([x, y, int(width), int(height)])
                centroids.append((centerX, centerY))
                confidences.append(float(confidence))
                
	# Excluding overlapping boxes and return the detection
    detection_no_boxes = cv2.dnn.NMSBoxes(boxes, confidences, MIN_CONFIDENCE, NMS_THRESHOLD)

    # Take boxes from image detection
    results = []
    if len(detection_no_boxes) > 0:  
        results = get_boxes(detection_no_boxes, boxes, confidences, centroids)

	# return the list of results
    return results

def get_boxes(detection_no_boxes, boxes, confidences, centroids):
    """
    This function returns the boxes coordinades from the detection
    """

    results = []
	# loop over the indexes we are keeping
    for i in detection_no_boxes.flatten():
	# extract the bounding box coordinates
        (x, y) = (boxes[i][0], boxes[i][1])
        (w, h) = (boxes[i][2], boxes[i][3])

        # update our results list to consist of the person
        # prediction probability, bounding box coordinates,
        # and the centroid
        res = (confidences[i], (x, y, x + w, y + h), centroids[i])
        results.append(res)
    return results

def print_in_file(file,text):
    """
    This function print the text param into the file param.
    """

    file_obj = open(file,'a')
    if(file_obj.writable()):
        file_obj.write(text)
        file_obj.close()

def get_input(args):
    """
    This function return the input from where the video is taken using the argument setted (args).
    """

    if args["camera"] == "True" :
        input_detection = 0
    else : 
        input_detection = args["video"]

    # Test if input is valid
    if test_input(input_detection) == False:
        exit()
    return input_detection

def start_detection(args):
    """
    This function start the detection from the video using the argument passed (args). 
    """

    input_detection = get_input(args)
    
    # Labels for naming objects
    labelsPath = "coco.names"
    LABELS = open(labelsPath).read().strip().split("\n")

    # Using pretrained models for the face
    weights_path = "yolov4-tiny.weights"
    config_path = "yolov4-tiny.cfg"

    model_faces = cv2.dnn.readNetFromDarknet(config_path, weights_path)
    
    # Extract layers
    layer_name = model_faces.getLayerNames()
    layer_name = [layer_name[i - 1] for i in model_faces.getUnconnectedOutLayers()]


    # Emotion pretrained models
    model_emotion = 'Model/emotion-ferplus-8.onnx'
    net_emotion = cv2.dnn.readNetFromONNX(model_emotion)
    
    # Set input video
    cap = cv2.VideoCapture(input_detection)
    print(f"input: {input_detection}")
    
    draw_detection(net_emotion,model_faces,layer_name,LABELS,cap)


def emotion(image, network):

    """
    This function return the detected emotion string from the image param using the network param.
    """

    #available emotions
    emotions = ['Neutral', 'Happy', 'Surprise', 'Sad', 'Anger', 'Disgust', 'Fear', 'Contempt']
    img_copy = image.copy()

    # Detect faces from the image.
    faces = fd.ssd_detect(img_copy, conf=0.2)

    # coordinates of the first face. 
    x,y,w,h = faces[0]

    # add padding to the detection
    padding = 3

    # Extract the Face from image with padding.
    face = img_copy[y-padding:y+h+padding,x-padding:x+w+padding]

    # Convert Image into Grayscale for better performance
    gray = cv2.cvtColor(face,cv2.COLOR_BGR2GRAY)

    # Resize the image for better performance
    resized_face = cv2.resize(gray, (64, 64))

    # Reshape the image into required format for the model 
    processed_face = resized_face.reshape(1,1,64,64)
    network.setInput(processed_face)

    Output = network.forward()

    # Compute softmax values for each sets of scores  
    expanded = np.exp(Output - np.max(Output))
    probablities =  expanded / expanded.sum()

    # Get the final probablities 
    prob = np.squeeze(probablities)

    # Use the max probability index to take the index from the array of avaiable emotions.
    predicted_emotion = emotions[prob.argmax()]

    return predicted_emotion

def draw_detection(net_emotion,model,layer_name,LABELS,cap):
    """
    This function draw the input with detected people.
    """
    
    
    frame_rate = 30 # max 8 fps al secondo
    previous_time = 0

    # Main loop of the application
    while True:

        # The people count
        persons = 0
        delta_time = time.time() - previous_time


        # Read the image from the input
        (grabbed, image) = cap.read()
        if delta_time > 1./frame_rate:
            previous_time = time.time()
            
            # Check if non grabbed the image
            if not grabbed:
                break
            image = imutils.resize(image, width=700)
            results = detect_from_image(image, model, layer_name, personidz=LABELS.index("person"))

            for res in results:
                persons += 1
                emotion_detected = True
                # Convert from array-value to Image obj
                image_copy = Image.fromarray(image) 

                # Extract face from original image
                image_cropped = image_copy.crop((res[1][0], res[1][1], res[1][2], res[1][3]))

                # Catch any possible error while detecting emotion from image
                try:
                    # Take emotion from face
                    face_emotion = emotion(np.array(image_cropped), net_emotion)
                    
                    
                    # Settings for the text
                    font = cv2.FONT_HERSHEY_SIMPLEX
                    org = (res[1][0],res[1][1])
                    fontScale = 1
                    color = (255, 0, 0)
                    thickness = 2

                    # Put the text of the emotion in the image
                    cv2.putText(image, face_emotion, org, font, fontScale, color, thickness, cv2.LINE_AA)
                except:     
                    emotion_detected = False
                    cv2.rectangle(image, (res[1][0],res[1][1]), (res[1][2],res[1][3]), (0, 255, 0), 2)
                    
                if emotion_detected :
                    cv2.rectangle(image, (res[1][0],res[1][1]), (res[1][2],res[1][3]), (0, 255, 0), 2)

                

            cv2.imshow("Detection",image)
            print(f"count: {persons}")

            # If output file is setted
            if isinstance(args["output"],str):
                date = datetime.now()
                text_file = f"{date};{persons}"
                print_in_file(args["output"],f"{text_file}\n")
            
            # Showing the detction
            cv2.imshow("Detection",image)
            
            # If button "ESC" is pressed
            key = cv2.waitKey(1)
            if key == 27:
                break

    cap.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    """
    This is the main function from where the program starts.
    """

    args = args_parser()
    if check_args(args):
        start_detection(args)