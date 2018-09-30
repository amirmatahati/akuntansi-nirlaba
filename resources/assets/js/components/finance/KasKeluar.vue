<template>
	<div id="myContainer">
	    <div class="card">
		    <h3 class="card-header text-white bg-primary no-margin">Kas Keluar <icon name="map"></icon></h3>
			<b-breadcrumb :items="items"/>
    		<div class="card-body">
        		<b-row>
            	<b-col sm="6">
						 			<b-row class="my-1">
                  		<b-col sm="4"><label for="input-small">Akun Kas :</label></b-col>
                      <b-col sm="8">
                      	<b-form-select v-model="akun_type" class="mb-3" v-on:change="getData">
														<option v-for="item in akun" :value="item.id" :key="item.id">
															{{item.perkiraan_akun}}
														</option>
												</b-form-select>
                      </b-col>
                  </b-row>
              	</b-col>
            <b-col sm="6">
						<b-row class="my-1">
                            <b-col sm="4"><label for="input-small">Saldo Kas :</label></b-col>
                            <b-col sm="8">
																<label>Rp. {{ formatPrice(total_saldo2)}}</label>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col sm="6">
                        <b-row class="my-1">
                            <b-col sm="4"><label for="input-small">Untuk :</label></b-col>
                            <b-col sm="8">
                                <b-form-input id="input-small" size="sm" type="text" placeholder="tujuan dana" v-model="sumber_dana"></b-form-input>
                            </b-col>
                        </b-row>
                        <b-row class="my-1">
                            <b-col sm="4"><label for="input-small">Note:</label></b-col>
                            <b-col sm="8">
                                <b-form-textarea id="textarea1"
                                                v-model="info"
                                                placeholder="Enter something"
                                                :rows="3"
                                                :max-rows="6">
                                </b-form-textarea>
                            </b-col>
                        </b-row>
                    </b-col>

                    <b-col sm="6">
                        <b-row class="my-1">
                            <b-col sm="4"><label for="input-small">No. Kas:</label></b-col>
                            <b-col sm="8">
                                <b-form-input id="input-small" size="sm" type="text" placeholder="No. Kas" v-model="no_kas" disabled="disabled"></b-form-input>
                                <input type="hidden" v-model="nojurnal">
                            </b-col>
                        </b-row>
                        <b-row class="my-1">
                            <b-col sm="4"><label for="input-small">Date:</label></b-col>
                            <b-col sm="8">
                                <flat-pickr v-model="date_added" :config="config" placeholder="Select a date"></flat-pickr>
                            </b-col>
                        </b-row>
                        <b-row class="my-1">
                            <b-col sm="4"><label for="input-small">Jumlah Diterima:</label></b-col>
                            <b-col sm="8">
                                <b-form-input id="input-small" size="sm" type="text" placeholder="Jumlah Diterima" v-model="jumlah_kas"></b-form-input>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>

				<b-row class="my-1">
				    <b-col sm="4">
						<div class="card">
							<h5 class="card-header text-white bg-primary no-margin">Perkiraan Akun <icon name="map"></icon></h5>
							<div class="card-body">
								<b-row>
									<b-col sm="12">
										<form v-on:submit.stop.prevent="getAkun">
											<b-input-group>
											<b-form-input id="exampleInput1"
												type="text"
												v-model="pencarian"
												placeholder="Keyword"></b-form-input>
												<b-input-group-append>
													<b-btn type="submit" variant="info"><icon name="search"></icon></b-btn>
												</b-input-group-append>
											</b-input-group>
										</form>
									</b-col>
									<br />
									<b-col sm="12">
										<br />
										<button type="submit" @click="PassData()" class="btn btn-success btn-sm">Pilih</button>
										<button type="submit" @click="reset()" class="btn btn-success btn-sm">Reset</button>
									</b-col>
								</b-row>
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Action</th>
												<th>Kode</th>
												<th>Name</th>
												<th>Sub Klasifikasi</th>
												<th>Klasifikasi</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="akuns in name_akun" v-bind:value="akuns.id" :key="akuns.id">
												<td> <label class="form-checkbox">
															<input type="checkbox" :value="akuns.id" v-model="selected" >
															<i class="form-icon"></i>
														</label></td>
												<td>{{ akuns.kode_akun }}</td>
												<td> {{ akuns.perkiraan_akun }}</td>
												<td>{{ akuns.sub_klasifikasi }}</td>
												<td>{{ akuns.klasifikasi}}</td>

											</tr>
										</tbody>
									</table>
								</div>
								<pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="getAkun()"></pagination>
							</div>
						</div>



					</b-col>
					<b-col sm="8" v-if="selected > '0'">
						<div class="card">
							<h5 class="card-header text-white bg-primary no-margin">Alokasi Dana</h5>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Kode</th>
												<th>Name</th>
												<th>Jumlah</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="d_akun in pass_data" v-bind:value="d_akun.id" :key="d_akun.id">
												<td> <input type="text" class="form-control" v-model="d_akun.kode_akun" disabled="disabled"></td>
												<td> <input type="text" class="form-control" v-model="d_akun.perkiraan_akun" disabled="disabled"></td>
												<td><input v-model="d_akun.jumlah_debet" @change="totals()" type="number" class="form-control" placeholder="Jumlah"></td>
											</tr>
										</tbody>
									</table>
							</div>
							<b-row class="my-1">
								<b-col sm="6"></b-col>
								<b-col sm="6">
									<div class="row">
										<label for="input-small" class="col-sm-6">Total Diterima:</label>
										<div class="col-sm-6">
												<label>Rp. {{ formatPrice(totalnya)}}</label>
										</div>
									</div>
								</b-col>
							</b-row>
							<hr></hr>
							<b-row class="my-1">
								<b-col sm="6"></b-col>
								<b-col sm="6">
									<div class="row">
										<label for="input-small" class="col-sm-6">Balance:</label>
										<div class="col-sm-6">
												<label>Rp. {{ formatPrice(balance) }}</label>
										</div>
									</div>
								</b-col>
							</b-row>
							<button id="submit" type="submit" @click="processForm()" class="btn btn-success">Insert</button>
							</div>
						</div>
					</b-col>
				</b-row>

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
                nojurnal: 0,
                total_saldo2: 0,
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
				date_added: '',
				info: '',
				name_akun: [],
				pass_data: '',
				selected:[],
				selectAll: false,
				jumlah_debet: '',
				jumlah_kredit: '',
				totalnya: 0,
				totalkredit: 0,
				balance: 0,
				pencarian: '',
				items: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Input Transaksi',
					to: '/input-transaksi'
				},{
					text: 'Kas Keluar',
					href: '#'
                }],
                sumber_dana: '',
                jumlah_kas: '',
                no_kas: '',
                akun: '',
                akun_type: '',
                last_page: 0,
                pagination: {
					'current_page': 0,
					'total': 0

                },

			}
		  },
		computed: {


		},
		mounted(){
		},
		created: function()
        {
            let uri = './kas-masuk';
            axios.get(uri).then((response) => {
                this.no_kas = response.data.nokas
                this.akun   = response.data.akun
								this.nojurnal	= response.data.nojurnal
            });

            let no_ju = './jurnal-add';
            axios.get(no_ju).then((response) => {
                this.nojurnal = response.data;
            });

            this.getAkun()

		},

		methods:{
            getData: function(e) {
                let akun_s		= './get-saldo-by-id/' + e;
				axios.get(akun_s).then((response) => {
					this.total_saldo2    = response.data
					console.log(this.total_saldo2)
				});
			},
			getAkun: function(){
				this.show()
				let akun_s		= './search-akun-asset?q='+ this.pencarian +'&page=' + this.pagination.current_page;
				axios.get(akun_s).then((response) => {
					this.name_akun = response.data.data.data
					this.pagination = response.data.pagination
				});
			},
			PassData: function(){
				this.show()
				axios.post('./search-akun-byid',
                {
					data: this.selected
				}).then((response) => {
					this.pass_data	= response.data
				});
			},
			select() {
				this.selected = [];
				if (!this.selectAll) {

					for (let i in this.items) {
						this.selected.push(this.items[i].id, this.item[i].kode, this.item[i].perkiraan_akun);
					}
				}
			},
			reset(){
				this.selected	= [];
			},
			show() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			totals: function() {
				let sum = 0;
                let kredit	= 0;
                //let jmlT    = 0;
				this.pass_data.forEach(function(d_akun) {
					sum += parseFloat(d_akun.jumlah_debet);
				});

				 this.totalnya      = sum
				 this.balance		= sum - this.jumlah_kas
			},
			processForm: function() {
          let tipe_kas            = 'Kas Keluar'

					document.getElementById("submit").disabled = true;
					axios.post('./insert-kas-masuk',
                    {
                        no_bukti   		: this.no_kas,
                        nojurnal        : this.nojurnal,
						keterangan		: this.info,
                        tgl_kas 		: this.date_added,
                        jumlah_kas      : this.jumlah_kas,
                        tipe_kas        : tipe_kas,
                        sumber_dana     : this.sumber_dana,
                        akun_type       : this.akun_type,
						data: this.pass_data,
					})
					.then((response) => {
						this.saleid	= response.data
						this.$router.push({name: 'JurnalList'});
					  });

            },
			formatPrice(value) {
				let val = (value/1).toFixed(2).replace('.', ',')
				return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
			}

		}

    }
</script>
