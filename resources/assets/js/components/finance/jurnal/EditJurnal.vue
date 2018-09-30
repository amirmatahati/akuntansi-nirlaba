<template>
	<div id="myContainer">
	    <div class="card">
		    <h3 class="card-header text-white bg-primary no-margin">Add Jurnal <icon name="map"></icon></h3>
			<b-breadcrumb :items="items"/>
    		<div class="card-body">
					<form method="post" v-on:submit.stop.prevent="processForm" id="row">
                <b-row class="my-1">
				    <b-col sm="4"><label for="input-small">No. Jurnal:</label></b-col>
					<b-col sm="8">
						<b-form-input id="input-small" size="sm" type="text" placeholder="No. Jurnal" v-model="jurnal.no_jurnal" disabled="disabled"></b-form-input>
					</b-col>
				</b-row>
				<b-row class="my-1">
				    <b-col sm="4"><label for="input-small">Date:</label></b-col>
					<b-col sm="8">
						<flat-pickr v-model="jurnal.date_jurnal" :config="config" placeholder="Select a date"></flat-pickr>
					</b-col>
				</b-row>
				<b-row class="my-1">
				    <b-col sm="4"><label for="input-small">Note:</label></b-col>
					<b-col sm="8">
						<b-form-textarea id="textarea1"
										 v-model="jurnal.info"
										 placeholder="Enter something"
										 :rows="3"
										 :max-rows="6">
						</b-form-textarea>
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
													<b-btn variant="info"><icon name="search"></icon></b-btn>
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
							<h5 class="card-header text-white bg-primary no-margin">Jurnal</h5>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Kode</th>
												<th>Name</th>
												<th>Debet</th>
												<th>Kredit</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="d_akun in pass_data" v-bind:value="d_akun.id" :key="d_akun.id">
												<td> <input type="text" class="form-control" v-model="d_akun.kode_akun" disabled="disabled"></td>
												<td> <input type="text" class="form-control" v-model="d_akun.perkiraan_akun" disabled="disabled"></td>
												<td>
													<input v-model="d_akun.jumlah_debet" @change="totals()" type="number" class="form-control" placeholder="Quantity">

												<td> <input type="text" class="form-control" @change="totals()" v-model="d_akun.jumlah_kredit"></td>
											</tr>
										</tbody>
									</table>
							</div>
							<b-row class="my-1">
								<b-col sm="6"></b-col>
								<b-col sm="6">
									<div class="row">
										<label for="input-small" class="col-sm-6">Total Debet:</label>
										<div class="col-sm-6">
											<b-form-input id="input-small" size="sm" type="text" placeholder="No. Jurnal" v-model="totalnya" disabled="disabled"></b-form-input>
										</div>
									</div>
								</b-col>
							</b-row>

							<b-row class="my-1">
								<b-col sm="6"></b-col>
								<b-col sm="6">
									<div class="row">
										<label for="input-small" class="col-sm-6">Total Kredit:</label>
										<div class="col-sm-6">
											<b-form-input id="input-small" size="sm" type="text" placeholder="No. Jurnal" v-model="totalkredit" disabled="disabled"></b-form-input>
										</div>
									</div>
								</b-col>
							</b-row>

							<b-row class="my-1">
								<b-col sm="6"></b-col>
								<b-col sm="6">
									<div class="row">
										<label for="input-small" class="col-sm-6">Balance:</label>
										<div class="col-sm-6">
											<b-form-input id="input-small" size="sm" type="text" placeholder="No. Jurnal" v-model="balance" disabled="disabled"></b-form-input>
										</div>
									</div>
								</b-col>
							</b-row>
							<button type="submit" @click="processForm()" class="btn btn-success">Insert</button>
							</div>
						</div>
					</b-col>

					<b-col sm="8" v-else>
						<div class="card">
							<h5 class="card-header text-white bg-primary no-margin">Jurnal</h5>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Kode</th>
												<th>Name</th>
												<th>Debet</th>
												<th>Kredit</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="detail in detailJurnal">
												<td> <input type="text" class="form-control" v-model="detail.kode_akun" disabled="disabled"></td>
												<td> <input type="text" class="form-control" v-model="detail.perkiraan_akun" disabled="disabled"></td>
												<td>
													<input v-model="detail.jumlah_debet" @change="totals()" type="number" class="form-control" placeholder="Quantity">

												<td> <input type="text" class="form-control" @change="totals()" v-model="detail.jumlah_kredit"></td>
											</tr>
										</tbody>
									</table>
							</div>

						<button type="submit" class="btn btn-success">Insert</button>
							</div>
						</div>
					</b-col>
				</b-row>
			</form>
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
				jurnal: '',
				detailJurnal: [],
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
				last_page: 0,
				pagination: {
					'current_page': 0,
					'total': 0

				},
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
					to: '/finance-menu'
				}, {
					text: 'Jurnal Umum',
					to: '/list-jurnal'
				},{
					text: 'Add Jurnal',
					href: '#'
				}]

			}
		  },
		computed: {


		},
		mounted(){
		},
		created: function()
        {
            let uri = './edit-jurnal/' + this.$route.params.id;
            axios.get(uri).then((response) => {
                this.jurnal 		= response.data.jurnal;
								this.detailJurnal		= response.data.detailJurnal
			});
			this.getAkun()
		},

		methods:{
			getAkun: function(){
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
			updateQuantity: function() {
				this.jumlah_debet = this.jumlah_debet + this.jumlah_kredit
			},
			totals: function() {
				let sum = 0;
				let kredit	= 0;
				this.pass_data.forEach(function(d_akun) {
					sum += parseFloat(d_akun.jumlah_debet);
					kredit	+= parseFloat(d_akun.jumlah_kredit);
				});

				 this.totalnya = sum
				 this.totalkredit	= kredit
				 this.balance		= sum - kredit
			},
			processForm: function() {
					axios.post('./update-jurnal/' + this.$route.params.id,
          {
						//detailJurnal				: this.detailJurnal,
						jurnal				: this.jurnal,
						data					: this.pass_data,

					})
					.then((response) => {
						this.saleid	= response.data
						this.$router.push({name: 'JurnalList'});
					  });

            }
		}

    }
</script>
