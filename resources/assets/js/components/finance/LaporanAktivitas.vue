<template>
	<div id="myContainer">
		<div class="page-header">
			<h3 class="page-title">
				LAPORAN AKTIVITAS <icon name="map"></icon>
			</h3>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
					<li class="breadcrumb-item active" aria-current="page">Laporan Aktivitas</li>
				</ol>
			</nav>
		</div>
	<div class="card">
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
								<ul class="no_list_built">
										<li><strong>PERUBAHAN ASET NETO TIDAK TERIKAT</strong>
												<ul>
						                <div v-html="neraca">

						                </div>
														<ul class="no_list_built">
																<li>
																		<span class="float-left"><strong>Jumlah Pendapatan</strong></span>
																		<strong><span class="float-right" v-html="formatPrice(total_tdk_terikat)"></span></strong>
																</li>
														</ul>
														<div v-html="bebanKantor"></div>
														<ul class="no_list_built">
																<li>
																		<span class="float-left"><strong>Jumlah Beban</strong></span>
																		<strong><span class="float-right" v-html="totalBebanKantor"></span></strong>
																</li>
														</ul>
														<div style="clear:both;"></div>
														<div v-html="bebanProgram"></div>
														<ul class="no_list_built">
																<li>
																		<span class="float-left"><strong>Jumlah Beban Program</strong></span>
																		<strong><span class="float-right" v-html="totalProgram"></span></strong>
																</li>
														</ul>

													</ul>
										</li>
										<div style="clear:both;"></div>
										<li>
												<span class="float-left">Kenaikan aset neto tidak terikat</span>
												<span class="float-right" v-html="AsetTidakTerikat"></span>
										</li>
								</ul>
								<div style="clear:both;margin: 20px"></div>
								<ul class="no_list_built">
										<li><strong>PERUBAHAN ASET NETO TERIKAT TEMPORER</strong>
												<ul>
													<div v-html="permanen"></div>
													<ul class="no_list_built">
															<li>
																	<span class="float-left"><strong>Jumlah Pendapatan</strong></span>
																	<strong><span class="float-right" v-html="b_permanen_t"></span></strong>
															</li>
													</ul>
													<div v-html="bebanTemporer"></div>
													<ul class="no_list_built">
															<li>
																	<span class="float-left"><strong>Jumlah Beban</strong></span>
																	<strong><span class="float-right" v-html="totalTemporer"></span></strong>
															</li>
													</ul>
												</ul>
										</li>
										<div style="clear:both;"></div>
										<li>
												<span class="float-left">Kenaikan aset neto terikat temporer</span>
												<span class="float-right" v-html="total_temporer"></span>
										</li>
								</ul>

								<div style="clear:both;margin: 20px"></div>
								<ul class="no_list_built">
										<li><strong>PERUBAHAN ASET NETO TERIKAT PERMANEN</strong>
												<ul>
														<div v-html="wakaf_t"></div>
														<ul class="no_list_built">
																<li>
																		<span class="float-left"><strong>Jumlah Pendapatan</strong></span>
																		<strong><span class="float-right" v-html="total_wakaf_sub"></span></strong>
																</li>
														</ul>
														<div style="clear:both;"></div>
														<li>
																<span class="float-left">Penurunan aset neto terikat permanen</span>
																<span class="float-right" v-html="total_wakaf_sub"></span>
														</li>
												</ul>
										</li>
								</ul>
								<ul class="no_list_built">
										<li>
												<span class="float-left"><strong>Saldo Aset Neto Akhir</strong></span>
												<strong><span class="float-right" v-html="saldo_AsetNeto"></span></strong>
										</li>
								</ul>
           </div>
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
				total_tdk_terikat		: 0,
				permanen						: '',
				b_permanen_t				: 0,
				totalTemporer				: '',
				wakaf_t							: '',
				AsetTidakTerikat		: 0,
				totalBebanKantor		: 0,
				bebanProgram				: '',
				totalProgram				: 0,
				bebanTemporer				: '',
				total_temporer			: 0,
				total_wakaf_sub			: 0,
				saldo_AsetNeto			: 0,
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
						this.total_tdk_terikat	= response.data.totalb
            this.periode          = response.data.periode
            this.bebanKantor      = response.data.beban
						this.totalBebanKantor	= response.data.total_beKantor
						this.bebanProgram			= response.data.programtdkTerikat
						this.totalProgram			= response.data.total_beProgram
						this.AsetTidakTerikat		= response.data.AsetTidakTerikat
						this.permanen						= response.data.pendTerikat
						this.b_permanen_t				= response.data.total_PendTerikat
						this.bebanTemporer			= response.data.bebanTemporer
						this.totalTemporer			= response.data.total_beban_tempore
						this.total_temporer			= response.data.total_temporer
						this.wakaf_t						= response.data.wakaf
						this.total_wakaf_sub		= response.data.total_wakaf_sub
						this.saldo_AsetNeto			= response.data.saldo_AsetNeto

						console.log(this.totalBebanKantor)
					})
					.catch(error => {
						console.log(error.response.data);
					});
			},
			formatPrice(value) {
				let val = (value/1).toFixed(2).replace('.', ',')
				return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
			}
  		}
    }
</script>
