<template>
	<div class="card">
        <h3 class="card-header text-white bg-primary no-margin">LABA RUGI <icon name="map"></icon></h3>
		<b-breadcrumb :items="items"/>
        <div class="card-body">
			<form @submit.prevent="labaRugi">
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

            <div v-if="periode > '0'">
                <h2 class="text-center">LABA RUGI</h2>
                <p class="text-center">Untuk Periode Yang Berakhir {{ periode }}</p>
                <div class="table-responsive">
                    <table class="table borderless">
												<tbody>
	                        <tr>
	                            <th colspan="2">Pendapatan</th>
	                        </tr>
												</tbody>
                        <tbody v-html="t_pendapatan">

												</tbody>

                        <tr style="color: #400000;font-weight: bold">
                            <td colspan="2">Total</td>
                            <td>Rp. {{ formatPrice(totalPendapatan1) }}</td>
                        </tr>
												<tbody>
	                        <tr style="border:none">
	                            <th colspan="2">Beban :</th>
	                        </tr>
												</tbody>
                        <tbody v-html="table2">

                        </tbody>
                        <tbody v-html="table3">

                        </tbody>
                        <tr style="color: #400000;">
                            <th colspan="2">Total Beban</th>
                            <th>Rp. {{ formatPrice(sublaba) }}</th>
                        </tr>
                        <tfoot style="background-color:#001a66;color:#fff;padding-left: 10px;">
                            <tr>
                                <th colspan="2">Laba</th>
                                <th>Rp. {{ formatPrice(laba) }}</th>
                            </tr>
                        </tfoot>
                    </table>
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
      	nama_akun       : [],
				items			: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Laba Rugi',
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
                totalPendapatan1: '',
                table2          : '',
                table3          : '',
                sublaba         : '',
                laba            : '',
								t_pendapatan		: ''
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
			labaRugi() {
                this.show()
				axios.get('./laba-rugi-priode?start='+ this.start + '&end='+ this.end)
					.then(response => {
                        this.nama_akun 	        = response.data.nama_akun
                        this.periode            = response.data.periode
                        this.totalPendapatan1   = response.data.totalPendapatan1
                        this.table2             = response.data.table2
                        this.table3             = response.data.table3
                        this.sublaba            = response.data.sublaba
                        this.laba               = response.data.laba
												this.t_pendapatan				= response.data.t_pendapatan


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
