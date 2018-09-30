<template>
	<div class="card">
        <h3 class="card-header text-white bg-primary no-margin">Saldo Akun <icon name="map"></icon></h3>
        <b-breadcrumb :items="items"/>
        <div class="card-body">
        <form @submit.prevent="GetAsset">
            <b-row>
                <b-col sm="6">
                    <flat-pickr v-model="start" :config="config" placeholder="Select a date"></flat-pickr>
                </b-col>
                <b-col sm="6">
                    <div class="input-group mb-3">
                        <flat-pickr v-model="end" :config="config" placeholder="Select end date"></flat-pickr>
                        <div class="input-group-append">
                        <button type="submit" class="btn btn-success btn-xs"><icon name="search"></icon></button>
                        </div>
                    </div>

                </b-col>

            </b-row>
		</form>
		<div id="container" style="min-width: 100%; height: 400px; margin: 0 auto"></div>
        </div>
    </div>
</template>

<script>
import Highcharts from 'highcharts';
	export default {
		http: {
            headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		},
		data(){
			return{
                start: '',
                end: '',
				endMonth: '',
				data_chart: {"forceDecimals": "1"},
                name_chrt:'',
                years_asset: '',
                title: '',
                config: {
				   enableTime: true,
					altFormat: "Y-m-d",
					altInput: true,
					toolbar: [
					  ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'],
					  ['Source', 'Maximize'],
						['Image']
					],
					height: 300
				 },
                 flatPickrConfig: { static: true },
                items: [{
					text: 'Home',
					to: '/'
				}, {
					text: 'Saldo Akun',
					href: '#'
				}]
			}
		  },
		mounted(){


		},
		created: function()
        {
           this.GetAsset()
        },

		methods: {
            GetAsset(){
                this.show()
                if(this.start == ''){
                    this.uri = './list-asset';
                    }else{
                        this.uri = './search-asset?start='+ this.start + '&end='+ this.end;
                }
                axios.get(this.uri).then(response => {
                    this.data_chart = response.data.data_chart
                    this.name_chrt = response.data.name_chrt
                    this.years_asset    = response.data.years_asset
                    if(this.start == ''){
                        this.title = 'Saldo Perkiraan Akun'
                    }else{
                        this.title = 'Saldo Perkiraan Akun Periode ' + this.years_asset
                    }

                        Highcharts.chart('container', {
                        chart: {
                            zoomType: 'x'
                        },
                        title: {

                            text: this.title


                        },
                        subtitle: {
                            text: document.ontouchstart === undefined ?
                                    'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
                        },
                        xAxis: {
                    categories: this.name_chrt
                },
                        yAxis: {
                            title: {
                                text: 'Exchange rate'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            area: {
                                fillColor: {
                                    linearGradient: {
                                        x1: 0,
                                        y1: 0,
                                        x2: 0,
                                        y2: 1
                                    },
                                    stops: [
                                        [0, Highcharts.getOptions().colors[0]],
                                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                    ]
                                },
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                },
                                lineWidth: 1,
                                states: {
                                    hover: {
                                        lineWidth: 1
                                    }
                                },
                                threshold: null
                            }
                        },

                        series: [{
                            type: 'area',
                            name: 'Rp.',
                            data: this.data_chart
                        }]
                    });



	            })
            },
            open() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			show() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			}
  		}
    }
</script>
