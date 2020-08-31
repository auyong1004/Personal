var randomScalingFactor = function () {
    return Math.round(Math.random() * 100);
};
var chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};


Vue.component("my-line-chart", {
    template: '<canvas :id="id" :width="width" :height="height" ></canvas>',
    props: {
        id: {
            type: String,
            default: 'lineCHART',
        },
        datasets: {
            type: Array,
            default: () => {
                return []; 
            }
        },
        width: {
            type: String,
            default: '800px'
        },
        height: {
            type: String,
            default: '500px'
        },
        config: {
            type: Object,
            default: () => {
                return {};
            }
        }
    },
    data: function () {
        return {
            defaultConfig: {
                type: 'line',
                data: {
                    datasets: [
                        {
                            label: 'My First dataset',
                            backgroundColor: chartColors.red,
                            borderColor: chartColors.red,
                            data: [
                                {
                                    x: "2020-02-07T14:25:31+08:00",
                                    y: -58
                                }, {
                                    x: "2020-02-09T14:25:31+08:00",
                                    y: -63
                                },
                                {
                                    x: "2020-02-11T14:25:31+08:00",
                                    y: 94
                                }, {
                                    x: "2020-02-12T14:25:31+08:00",
                                    y: 66
                                }
                            ],
                            fill: false,
                        }
                    ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    responsive: true,
                    title: {
                        display: true,
                        //text: 'Chart.js Time Point Data'
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            display: true,
                            time: {

                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Date'
                            },
                            ticks: {
                                major: {
                                    fontStyle: 'bold',
                                    fontColor: '#FF0000'
                                }
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'value'
                            }
                        }]
                    }
                }
            }
        }
    },
    computed: {

    },
    watch: {
        config: {
            immediate: true,
            handler: function (config) {
                for (const key in this.defaultConfig) {
                    if (config.hasOwnProperty(key)) this.defaultConfig[key] = config[key];
                }
                if (window.myLine != undefined) window.myLine.update();

            }
        },
        datasets: {
            immediate: true,
            handler: function (datasets) {
                this.defaultConfig.data.datasets = datasets;
                if (window.myLine != undefined) window.myLine.update();
            }
        }
    },
    methods: {
        create: function () {
            let isError = false;
            this.defaultConfig.data.datasets = this.datasets;
            /*
            if(document.getElementById(this.id)){
                return;
            }
            */
            if (this.id == undefined || this.id == null) isError = true;
            if (document.getElementById(this.id) == null) isError = true;
            if (isError) {
                console.error('Invalid Id on canvas')
                return;
            }
            var ctx = document.getElementById(this.id).getContext('2d');
            window.myLine = new Chart(ctx, this.defaultConfig);

        }
    },
    mounted: function () {
        this.create();
    },
    created: function () { },


});

/*
{
                        label: "Dataset with string point data",
                        backgroundColor: "rgba(255, 99, 132, 0.5)",
                        borderColor: "rgb(255, 99, 132)",
                        fill: false,
                        data: [{
                                x: "2020-02-07T14:25:31+08:00",
                                y: -58
                            }, {
                                x: "2020-02-09T14:25:31+08:00",
                                y: -63
                            },
                            {
                                x: "2020-02-11T14:25:31+08:00",
                                y: 94
                            }, {
                                x: "2020-02-12T14:25:31+08:00",
                                y: 66
                            }
                        ]
                    }
*/