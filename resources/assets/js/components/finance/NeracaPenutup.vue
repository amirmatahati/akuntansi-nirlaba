<template>
	<div id="myContainer">
		<div class="page-header">
			<h3 class="page-title">
				POSISI KEUANGAN <icon name="map"></icon>
			</h3>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
					<li class="breadcrumb-item active" aria-current="page">Posisi Keuangan</li>
				</ol>
			</nav>
		</div>
	<div class="card">
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

                <h2 class="text-center">LAPORAN POSISI KEUANGAN</h2>
                <p class="text-center">Untuk Periode Yang Berakhir {{ periode }}</p>
                <b-row>
                    <b-col sm="6">
                        <div class="table-responsive">
                            <table class="table borderless">
                                <tbody>
                                    <tr>
                                        <th colspan="2">Asset</th>
                                    </tr>
                                </tbody>
                                    <tbody v-html="table1">
                                    </tbody>
                                <tbody>
                                    <tr style="color: #ff0000;">
                                        <th>Total Asset Lancar</th>
                                        <th>Rp. {{ formatPrice(jumlah) }}</th>
                                    </tr>
                                </tbody>
                                <tbody v-html="table2">

                                </tbody>
                                <tr style="color: #ff0000;">
                                    <th>Total Asset Tetap</th>
                                    <th>Rp. {{ formatPrice(totalAssetTetap)}}</th>
                                </tr>
                                <tfoot style="background-color:#001a66;color:#fff;">
                                    <tr>
                                        <th>Total Asset</th>
                                        <th>Rp. {{ formatPrice(total_asset)}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </b-col>
                    <b-col sm="6">
                        <div class="table responsive">
                            <table class="table">
                                <tr>
                                    <th>LIABILITAS</th>
                                </tr>
                                <tbody v-html="table3">

                                </tbody>
                                <tr style="color: #ff0000;">
                                    <th>Total Liabilitas </th>
                                    <th>Rp. {{ formatPrice(totalLibilitas)}}</th>
                                </tr>
                                <tr>
                                    <th>Aset Bersih</th>
                                </tr>
                                <tr>
                                    <td>Aset Bersih</td>
                                    <td>Rp. {{ formatPrice(modal_akhir) }}</td>
                                </tr>
                                <tr style="color: #ff0000;">
                                    <th>Total Equitas</th>
                                    <th>Rp. {{ formatPrice(totalLaba) }}</th>
                                </tr>
                                <tfoot style="background-color:#001a66;color:#fff;">
                                    <tr>
                                        <th>TOTAL LIABILITAS Dan Asset Bersih</th>
                                        <th>Rp. {{ formatPrice(penambahan_modalakhir) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </b-col>
                </b-row>
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
                table1          : [],
				items			: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Posisi Keuangan',
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
                periode                 : '',
                jumlah                  : '',
                table2                  : '',
                totalAssetTetap         : '',
                total_asset             : '',
                table3                  : '',
                totalLibilitas          : '',
                modal_akhir             : '',
                totalLaba               : '',
                penambahan_modalakhir   : '',
                name_asset_lancar       : '',
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
				axios.get('./asset-view-date-in?start='+ this.start + '&end='+ this.end)
					.then(response => {
                        this.table1                 = response.data.table1
                        this.periode                = response.data.periode
                        this.jumlah                 = response.data.jmlh
                        this.table2                 = response.data.table2
                        this.totalAssetTetap        = response.data.totalAssetTetap
                        this.total_asset            = response.data.total_asset
                        this.table3                 = response.data.table3
                        this.totalLibilitas         = response.data.totalLibilitas
                        this.modal_akhir            = response.data.modal_akhir
                        this.totalLaba              = response.data.totalLaba
                        this.penambahan_modalakhir  = response.data.penambahan_modalakhir
                        this.name_asset_lancar      = response.data.name_asset_lancar

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
