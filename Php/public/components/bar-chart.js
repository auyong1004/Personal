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


Vue.component("my-bar-chart", {
    template: `<canvas :id="id" style="display: block" :width="width" :height="height"  class="chartjs-render-monitor"></canvas>`,
    props: {
        id: {
            type: String,
            default: 'barChart',
        },
        datasets: {
            type: Array,
            default: () => {
                return [];
            }
        },
        width: {
            type: String,
            default: '800'
        },
        height: {
            type: String,
            default: '250'
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
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [
                        {
                            label: 'Dataset 1',
                            backgroundColor: "rgba(255, 99, 132, 0.5)",
                            borderColor: chartColors.red,
                            borderWidth: 1,
                            data: [
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor()
                            ]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Bar Chart'
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

                if (window.myBar != undefined) window.myBar.update();

            }
        },
        datasets: {
            immediate: true,
            handler: function (datasets) {
                if(datasets.length==0)return;
                this.defaultConfig.data.datasets = datasets;
                if (window.myBar != undefined) window.myBar.update();
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
            window.myBar = new Chart(ctx, this.defaultConfig);

        }
    },
    mounted: function () {
        this.create();
        console.log(this.defaultConfig)
    },
    created: function () { 

    },


});
