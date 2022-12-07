class Canva{
  constructor(id){
    this.id = id;
  }

  showChart($labels, $data){
    this.chart =  new Chart( $("#line-chart").get(0).getContext("2d"), {
      type: 'line',
      data: {
        labels: $labels,
        datasets: [
          { 
            data: $data,
            label: "Persone",
            borderColor: "#14213d",
            fill: true
          }
        ]
      },
      options: {
        animation: {
          duration: 0
      },
        responsive: true,
          legend: {
            position: 'bottom',
          },
          hover: {
            mode: 'label'
          },
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Data e orario'
              }
            }],
            yAxes: [{
              display: true,
              ticks: {
                beginAtZero: true,
                stepSize: 1
              } 
            }]
          },
          title: {
            display: true,
            text: 'Guest Data Logger'
          }
        }
      }
    );
    console.log(this.chart);
  }

  addValueChart($label,$data){
    if(this.chart != null){
      this.chart.data.labels.push($label);
      this.chart.data.datasets[0].data.push($data);
      this.chart.update();
    }else{
      showChart([$label],[$data]);
    }
  }
}