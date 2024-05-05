# Guest Data Logger
Author: Gioele Cavallo

## Info

This project was created in order to be able to count people in a pavilion through the use of AI and consult visit data conveniently from a website.

--- 
### Links:
- [Site](5_Applicativo/GuestDataLogger/)

- [Code](5_Applicativo/Python/)

- [Documentation](3_Documetazione/)



---
<i>
<b>Discalimer:</b> 
Execution may change on differents systems. Windows was taken as the model for this example so remember to change paths and commands in order to make them work in your system.</i>
<br>
<br>

### Install:
- Clone the project using <b> git clone http://gitsam.cpt.local/2022_2023_1_semestre/guest-data-logger.git \Repo\ </b>.
- Move to the Laravel directory (.\Repo\GuestDataLogger) and rename <b>.env.example</b> into <b>.env</b>
- Enter into .env and change <b>DB_CONNECTION</b> to <b>sqlite</b> and <b>DB_DATABASE</b> with the absolute path of the sqlite db.
- Run <b>composer install </b>.
- Migrate the database using: <b>php artisan migrate</b>.
- Then you have to install the requirements for python and create a <b>venv</b>.
<!-- 
https://carbon.now.sh/?bg=rgba%28146%2C176%2C196%2C1%29&t=solarized+light&wt=none&l=application%2Fx-sh&width=680&ds=true&dsyoff=20px&dsblur=68px&wc=true&wa=true&pv=55px&ph=56px&ln=false&fl=1&fm=Hack&fs=14px&lh=133%25&si=false&es=2x&wm=false&code=%253E%2520git%2520clone%2520http%253A%252F%252Fgitsam.cpt.local%252F2022_2023_1_semestre%252Fguest-data-logger.git%2520%255CRepo%255C%250A%253E%2520cd%2520%255CRepo%255CGuestDataLogger%250A%253E%2520cd%2520composer%2520install%250A%253E%2520cd%2520..%255C..%255CPython%255C%250A%253E%2520.%255Cvenv%255CScripts%255Cactivate%250A%28venv%29%2520%253E%2520pip%2520install%2520-r%2520requirements%2520
-->
<div style="text-align:center">
<img src="images/install.svg" alt="start python program"/>
</div>
<br> 
<br>

### Usage:


- Then start final.py and set the output file in <b>5_Applicativo\GuestDataLogger\public\data\ </b>. 
<!--
https://carbon.now.sh/?bg=rgba%28146%2C176%2C196%2C1%29&t=solarized+light&wt=none&l=application%2Fx-sh&width=680&ds=true&dsyoff=20px&dsblur=68px&wc=true&wa=true&pv=55px&ph=56px&ln=false&fl=1&fm=Hack&fs=14px&lh=133%25&si=false&es=2x&wm=false&code=%253E%2520cd%2520Python%255C%2520%250A%253E%2520.%255Cvenv%255CScripts%255Cactivate%250A%28venv%29%2520%253E%2520python.exe%2520final.py%2520-c%2520True%2520-o%2520..%255CGuestDataLogger%255Cpublic%255Cdata%255C%2520
-->
<div style="text-align:center">
<img src="images/Python_start.svg" alt="start python program"/>
</div>
<br> 
<br>

- After the program is running start the web service. 
Now you can browse to localhost:8000 and enjoy the result of real time Guest Data Logger. 
<br> 
<br>
<!--
https://carbon.now.sh/?bg=rgba%28146%2C176%2C196%2C1%29&t=solarized+light&wt=none&l=application%2Fx-sh&width=680&ds=true&dsyoff=20px&dsblur=68px&wc=true&wa=true&pv=55px&ph=56px&ln=false&fl=1&fm=Hack&fs=14px&lh=133%25&si=false&es=2x&wm=false&code=%253E%2520cd%2520.%255CGuestDataLogger%255C%2520%250AGuestDataLogger%253E%2520php%2520artisan%2520serve
-->
<div style="text-align:center">
<img src="images/Laravel_start.svg" alt="start python program" style="width: 60%; height:60%"/>
</div>
<br>
<br>

---
### Admin panel:

The default login credentials are: user-> admin, password-> admin.
Once logged in, it is possible to change the parameters of the graphic display:

- Min interval: is the minimum time for which data are taken.
- Max interval: is the maximum time for which data are taken.
- Pick up time: is the time in seconds for which the data are collapsed. If not set statistics will be done on days.
- File: is the file from where data are taken.
