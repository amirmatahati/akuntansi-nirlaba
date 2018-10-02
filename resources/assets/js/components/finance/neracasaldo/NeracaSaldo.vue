<template>
	<div id="myContainer">
		<div class="page-header">
			<h3 class="page-title">
				Neraca Saldo <icon name="map"></icon>
			</h3>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
					<li class="breadcrumb-item active" aria-current="page">Neraca Saldo</li>
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
                <h2 class="text-center">Neraca Saldo Sebelum Penyesuaian</h2>
                <p class="text-center">Untuk Periode Yang Berakhir {{ periode }}</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="2">Nama Akun</th>
                                <th>Debet</th>
                                <th >Kredit</th>
                            </tr>
                        </thead>
                        <tbody v-html="neraca">

                        </tbody>
                        <tfoot>
							<tr style="background-color:#001a66; color: #ffffff;">
								<th colspan="2">Total</th>
								<th>Rp. {{ formatPrice(totalb) }}</th>
								<th colspan="2">Rp. {{ formatPrice(totalb) }}</th>
							</tr>
						</tfoot>
                    </table>
                </div>
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
				axios.get('./neraca-saldo-priode?start='+ this.start + '&end='+ this.end)
					.then(response => {
                        this.neraca 	        = response.data.html
                        this.periode            = response.data.periode
                        this.totalb             = response.data.totalb


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
