<script>
function toggleDateSearch()
{
    var checkBox = document.getElementById("customSwitch8");
    const div = document.getElementById('dateSearch');
    if (div.style.display === 'none'){
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
//     const div = document.getElementById('dateSearch');
//     if (div.style.display === 'none') {
//         div.style.display = 'block';
// } else {
//         div.style.display = 'none';
// }

}
var ctx = document.getElementById('myChart').getContext("2d");;
var gradient = ctx.createLinearGradient(0, 0, 0, 400)
gradient.addColorStop(0, '#978efc')
gradient.addColorStop(1, '#dedbfb')
var myChart = new Chart(ctx, {
type: 'line',
data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [
        {
        label: '# of Votes',
        data: [9, 13, 7, 8, 19, 11],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 4,
        fill: false,
        borderColor: '#05c3fb',
    
    },    
    {
        label: '# of Votes',
        data: [12, 19, 12, 18, 10, 11],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 4,
        fill: true,
        borderColor: '#6c5ffc',
        backgroundColor : gradient,
    }
]
},
options: {
    plugins: {
        legend: {
            display: false
        },
    },
    tension : 0.2,
    scales: {
        x: {
            display: false,
        }
    }
}
});
var card1 = document.getElementById('cardChart').getContext("2d");;

var cardChart = new Chart(card1, {
type: 'bar',
data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [
    {
        data: [12, 19, 12, 18, 10, 11],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1,
        fill: true,
        borderColor: '#6cdafa',
        backgroundColor : '#caf0fb',
        borderRadius: 5,
    }
]
},
options: {
    tension : 0.2,
    offset : false,
    scales: {
        y: {
            beginAtZero: true,
            display: false
        },
        x: {
            display: false
        }
    },
    plugins: {
        legend: {
            display: false
        },
    }
}

});

var card2 = document.getElementById('chart2').getContext("2d");

var chart2 = new Chart(card2, {
type: 'line',
data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [
    {
        data: [12, 19, 12, 8, 2, 6],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 3,
        fill: false,
        borderColor: '#f46ef4',
        borderRadius: 5,
    }
]
},
options: {
    tension : 0.2,
    offset : false,
    scales: {
        y: {
            beginAtZero: true,
            display: false
        },
        x: {
            display: false
        }
    },
    plugins: {
        legend: {
            display: false
        },
    },
    elements: {
                point:{
                    radius: 0
                }
            }
}

});


var card3 = document.getElementById('chart3').getContext("2d");

var chart3 = new Chart(card3, {
type: 'bar',
data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [
    {
        data: [12, 19, 12, 8, 2, 6],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 3,
        fill: true,
        borderColor: '#4ecc48',
    }
]
},
options: {
    tension : 0.2,
    offset : false,
    scales: {
        y: {
            beginAtZero: true,
            display: false
        },
        x: {
            display: false
        }
    },
    plugins: {
        legend: {
            display: false
        },
    },
    elements: {
                point:{
                    radius: 0
                }
            }
}

});

var card4 = document.getElementById('chart4').getContext("2d");

var chart4 = new Chart(card4, {
type: 'line',
data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [
    {
        data: [12, 19, 12, 8, 2, 6],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 3,
        fill: false,
        borderColor: '#f7ba48',
    }
]
},
options: {
    tension : 0.2,
    offset : false,
    scales: {
        y: {
            beginAtZero: true,
            display: false
        },
        x: {
            display: false
        }
    },
    plugins: {
        legend: {
            display: false
        },
    },
    elements: {
                point:{
                    radius: 0
                }
            }
}

});
</script>