<template>
	<div class="card">
        <h3 class="card-header text-white bg-primary no-margin">Laporan Aktivitas <icon name="map"></icon></h3>
		<b-breadcrumb :items="items"/>
        <div class="card-body">
			<form @submit.prevent="neracaSaldo">
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

            <div v-if="neraca > '0'">
                <h2 class="text-center">Laporan Aktivitas</h2>
                <p class="text-center">Untuk Periode Yang Berakhir {{ periode }}</p>
                <div v-html="neraca">

                </div>
								<div v-html="bebanKantor"></div>
								<div v-html="bebanProgram"></div>
								<ul class="no_list_built">
										<li><span class="float-left">Kenaikan aset neto tidak terikat</span>
										<span class="float-right" v-html="total_tdk_terikat"></span></li>
								</ul>
								<br />
								<div v-html="permanen"></div>
								<div v-html="b_permanen_t"></div>
								<ul class="no_list_built">
										<li><span class="float-left">Kenaikan aset neto terikat temporer</span>
										<span class="float-right" v-html="totalTemporer"></span></li>
								</ul>
								<div v-html="wakaf_t"></div>
								<ul class="no_list_built">
										<li><span class="float-left"><strong>Total kenaikan (penurunan) Aset Neto</strong></span>
										<span class="float-right" v-html="total_kenaikan"></span></li>
								</ul>
           </div>
        </div>
    </div>
</template>

<script>
	export default {
		http: {
            headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		},
		data(){
			return{
        neraca      : [],
				bebanKantor					: '',
				bebanProgram				: '',
				total_tdk_terikat		: 0,
				permanen						: '',
				b_permanen_t				: '',
				totalTemporer				: '',
				wakaf_t							: '',
				total_kenaikan			: '',
				items			: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Neraca Saldo',
					href: '#'
                }],
                start           : '',
                end             : '',
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
                periode         : '',
                totalb          : '',
			}
		  },
		mounted(){


		},
		created: function()
        {

        },

		methods: {

      open() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			show() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			neracaSaldo() {
				this.show()
				axios.get('./aktivitas-transaksi?start='+ this.start + '&end='+ this.end)
				.then(response => {
            this.neraca 	        = response.data.html
            this.periode          = response.data.periode
            this.bebanKantor      = response.data.bebanKantor
						this.bebanProgram			= response.data.bebanProgram
						this.total_tdk_terikat	= response.data.total_tdk_terikat
						this.permanen						= response.data.permanen
						this.b_permanen_t				= response.data.b_permanen_t
						this.totalTemporer			= response.data.totalTemporer
						this.wakaf_t						= response.data.wakaf_t
						this.total_kenaikan			= response.data.total_kenaikan
					})
					.catch(error => {
						console.log(error.response.data);
					});
			},
			formatPrice(value) {
				let val = (value/1).toFixed(2).replace('.', ',')
				return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
			}
  		}
    }
</script>
