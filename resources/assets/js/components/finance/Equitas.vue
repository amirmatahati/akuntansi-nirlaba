<template>
	<div class="card">
        <h3 class="card-header text-white bg-primary no-margin">Ekuitas <icon name="map"></icon></h3>
		<b-breadcrumb :items="items"/>
        <div class="card-body">
			<form @submit.prevent="Ekuitas">
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
                <h2 class="text-center">EKUITAS</h2>
                <p class="text-center">Untuk Periode Yang Berakhir {{ periode }}</p>
                <div class="table-responsive">
                    <table class="table borderless">
                        <tbody>
                            <tr>
                                <th colspan="3">Modal Awal</th>
                            </tr>
                            <tr>
                                <td colspan="2">{{ nama_akun }}</td>
                                <td>Rp. {{ formatPrice(Mkredit) }}</td>
                            </tr>
                            <tr>
                                <td>Laba Bersih</td>
                                <td v-if="laba > 0">Rp. {{ formatPrice(laba) }}</td>
                            </tr>
                            <tr>
                                <td>{{ prive_name }}</td>

                                <td v-if="total == nostring" colspan="2">Rp. {{ formatPrice(nostring) }}</td>


                                <td  v-else colspan="2">Rp. {{ formatPrice(nostring) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Penambahan Modal</th>
                                <th>Rp. {{ formatPrice(penambahan_modal) }}</th>
                            </tr>
                            <tr style="background-color:#001a66;color:#fff;">
                                <th colspan="2" style="padding-left: 10px;">Modal Akhir</th>
                                <th>Rp. {{ formatPrice(modal_akhir) }}</th>
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
					text: 'Ekuitas',
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
                Mkredit         : '',
                laba            : '',
                prive_name      : '',
                total           : '',
                nostring        : '',
                penambahan_modal: '',
                modal_akhir     : ''
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
			Ekuitas() {
                this.show()
				axios.get('./serach-perubahan-modal?start='+ this.start + '&end='+ this.end)
					.then(response => {
                        this.nama_akun 	        = response.data.nama_akun
                        this.periode            = response.data.periode
                        this.Mkredit            = response.data.Mkredit
                        this.laba               = response.data.laba
                        this.prive_name         = response.data.prive_name
                        this.total              = response.data.total
                        this.nostring           = response.data.nostring
                        this.penambahan_modal   = response.data.penambahan_modal
                        this.modal_akhir        = response.data.modal_akhir


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
