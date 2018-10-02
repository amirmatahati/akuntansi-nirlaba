<template>
	<div id="myContainer">
	<div class="page-header">
		<h3 class="page-title">
			List Jurnal Umum<icon name="map"></icon>
		</h3>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
				<li class="breadcrumb-item active" aria-current="page">urnal Umum</li>
			</ol>
		</nav>
	</div>
	<div class="card">
      <div class="card-body">
				<b-row>
					<b-col sm="6">
						<router-link v-bind:to="{name: 'JurnalAdd'}">
							<button type="button" class="btn btn-sm btn-info" ><icon name="plus"></icon></button>
						</router-link>
					</b-col>
					<b-col sm="6">
						<form v-on:submit.stop.prevent="search_jurnal">
							<b-input-group>
								<flat-pickr v-model="pencarian_jurnal" :config="config" placeholder="Select a date"></flat-pickr>
								<b-input-group-append>
									<button type="submit" class="btn btn-sm btn-info" ><icon name="search"></icon></button>
								</b-input-group-append>
							</b-input-group>
						</form>
					</b-col>
					<br />

				</b-row>
            <div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>No. Jurnal</th>
							<th>Nama Akun</th>
							<th>Ket.</th>
							<th>Debet</th>
							<th>Kredit</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(datas, index) in jurnal" :key="datas.id">
							<td> {{ datas.date_added }} </td>
							<td> {{ datas.no_jurnal }} </td>
							<td> {{ datas.perkiraan_akun }} </td>
							<td>{{ datas.info }}</td>
							<td>Rp. {{ formatPrice(datas.jumlah_debet) }} </td>
							<td>Rp. {{ formatPrice(datas.jumlah_kredit) }} </td>
							<td>
									<button @click="Edit(index)" class="btn btn-success btn-sm"><icon name="edit"></icon></button>
							</td>
						</tr>
					</tbody>
					<footer>
						<tr>
							<th>Total Debet</th>
							<th>Total Kredit</th>
						</tr>
						<tr>
							<th>Rp. {{ formatPrice(sumdebets) }}</th>
							<th>Rp. {{ formatPrice(sumkredits) }}</th>
						</tr>
					</footer>
				</table>
            </div>
			<pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="jurnalIndex()"></pagination>
        </div>

				<b-modal id="modal1" size="lg" title="Edit Jurnal">
					<div class="my-4">
						<b-row class="my-1">
						    <b-col sm="12"><label for="input-small">Date:</label></b-col>
							<b-col sm="12">
								<flat-pickr v-model="update_jurnal_detail.date_added" :config="config" placeholder="Select a date"></flat-pickr>
							</b-col>
						</b-row>
						<div v-if="edit_akun">
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Kode Akun:</label></b-col>
									<b-col sm="12">
										<b-col sm="12"><label for="input-small">Kode Akun:</label></b-col>
										<b-col sm="12">
											<b-input-group>
											<b-form-input id="exampleInput1"
												type="text"
												v-model="edit_akun.kode_akun"
												placeholder="Keyword"></b-form-input>
												<b-input-group-append>
													<b-button @click="ShowAkun" variant="info"><icon name="edit"></icon></b-button>
												</b-input-group-append>
											</b-input-group>
										</b-col>
									</b-col>
							</b-row>
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Nama Akun:</label></b-col>
								<b-col sm="12">
										<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="edit_akun.perkiraan_akun"></b-form-input>

								</b-col>
							</b-row>
						</div>
						<div v-else>
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Kode Akun:</label></b-col>
									<b-col sm="12">
										<b-input-group>
										<b-form-input id="exampleInput1"
											type="text"
											v-model="update_jurnal_detail.kode_akun"
											placeholder="Keyword"></b-form-input>
											<b-input-group-append>
												<b-button @click="ShowAkun" variant="info"><icon name="edit"></icon></b-button>
											</b-input-group-append>
										</b-input-group>
									</b-col>
							</b-row>
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Nama Akun:</label></b-col>
								<b-col sm="12">
										<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="update_jurnal_detail.perkiraan_akun"></b-form-input>

								</b-col>
							</b-row>
						</div>
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Debet:</label></b-col>
								<b-col sm="12">
										<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="update_jurnal_detail.jumlah_debet"></b-form-input>
								</b-col>
							</b-row>
							<b-row class="my-1">
									<b-col sm="12"><label for="input-small">Kredit:</label></b-col>
								<b-col sm="12">
										<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="update_jurnal_detail.jumlah_kredit"></b-form-input>
								</b-col>
							</b-row>
							<button type="submit" @click="UpdateJurnal(update_jurnal_detail.id)" class="btn btn-primary">Save changes</button>
					</div>
				</b-modal>



				<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Akun Perkiraan</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
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
											<tr v-for="(akuns,index) in name_akun" v-bind:value="akuns.id_akun" :key="akuns.id_akun">
												<td> <button @click="Pass_datas(index)" class="btn btn-success btn-sm"><icon name="mouse-pointer"></icon></button></td>
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
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				        <button type="button" id="submit" class="btn btn-primary">changes</button>
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
			   last_page		: 0,
			   pagination		: {
					'current_page': 0,
					'total': 0

				},
				jurnal			: '',
				items			: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Jurnal Umum',
					href: '#'
				}],
				name_akun:'',
				pencarian: '',
				edit_akun: '',
				update_jurnal_detail: [],
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
				sumdebets: 0,
				sumkredits: 0,
				pencarian_jurnal: ''

			}
		  },
		mounted(){


		},

		created: function()
        {
           this.jurnalIndex()
        },

		methods: {
			getAkun: function(){
				let akun_s		= './search-akun-asset?q='+ this.pencarian +'&page=' + this.pagination.current_page;
				axios.get(akun_s).then((response) => {
					this.name_akun = response.data.data.data
					this.pagination = response.data.pagination
					this.sumdebets	= response.data.data.sumdebet
					this.sumkredits	= response.data.data.sumkredit

				});
			},
      open() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			show() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			jurnalIndex() {
				this.show()
				let jurnal			= './list-jurnal?&page=' + this.pagination.current_page;
				axios.get(jurnal)
					.then(response => {
						this.pagination = response.data.pagination
						this.jurnal	= response.data.data.data
						this.sumdebets	= response.data.sumdebet
						this.sumkredits	= response.data.sumkredit
						console.log(this.sumdebets)
						this.show()
					})
					.catch(error => {
						console.log(error.response.data);
					});
			},
			search_jurnal() {
				let jurnal			= './list-jurnal?q='+ this.pencarian_jurnal +'&page=' + this.pagination.current_page;

				axios.get(jurnal)
					.then(response => {
						this.pagination = response.data.pagination
						this.jurnal	= response.data.data.data
						this.show()
					})
					.catch(error => {
						console.log(error.response.data);
					});
			},
			formatPrice(value) {
				let val = (value/1).toFixed(2).replace('.', ',')
				return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
			},
			Edit: function(index){
				$("#modal1").modal("show");
				this.update_jurnal_detail = this.jurnal[index];
			},
			ShowAkun:function(){
				$("#modal2").modal("show");
				let akun_s		= './search-akun?q='+ this.pencarian +'&page=' + this.pagination.current_page;
				axios.get(akun_s).then((response) => {
					this.name_akun = response.data.data.data
					this.pagination = response.data.pagination
					console.log(this.name_akun)
				});
			},
			Pass_datas: function(index){
				$("#modal2").modal("hide");
				$("#modal1").modal("show");
				this.edit_akun = this.name_akun[index];
			},
			UpdateJurnal: function(id)
			{
					let uri = './update-jurnal/'+ id ;
					document.getElementById("submit").disabled = true;
					if(this.edit_akun.id > 0){
							var perkiraan_id1		= this.edit_akun.id;
					}else{
							var perkiraan_id1		= 0;
					}
					alert(this.edit_akun.id)

					 axios.post(uri,{
						perkiraan_id			: this.update_jurnal_detail.id_perkiraan,
						jumlah_debet			: this.update_jurnal_detail.jumlah_debet,
						date_added				: this.update_jurnal_detail.date_added,
						jumlah_kredit			: this.update_jurnal_detail.jumlah_kredit,
						jurnal_id					: this.update_jurnal_detail.jurnal_id,
						perkiraan_id1			: perkiraan_id1

					 }).then((response) => {
          			$('#submit').removeAttr('disabled');
								this.$swal({
				          title: 'Success Update',
				          input: 'text',
				          showCloseButton: true,
				        });
                $("#modal1").modal("hide");
					  });


        },
      countDownChanged (dismissCountDown) {
			  this.dismissCountDown = dismissCountDown
			},
			render() {
				  props: {
				update_jurnal_detail: this.update_jurnal_detail
			  }
			}
  	}
  }
</script>
