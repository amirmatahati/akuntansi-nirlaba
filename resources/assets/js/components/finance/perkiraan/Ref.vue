<template>
	<div id="myContainer">

	<div class="card">
		<h3 class="card-header text-white bg-primary no-margin">Perkiraan Akun <icon name="map"></icon></h3>
		<b-breadcrumb :items="items"/>
		<div class="card-body">
            <b-alert variant="danger"
						 dismissible
						 fade
						 :show="showDismissibleAlert"
						 @dismissed="showDismissibleAlert=false">
				  Dismissible Alert!
				</b-alert>
			<div class="row">
				<div class="form-group">
					<router-link v-bind:to="{name: 'AddRef'}">
						<button type="button" class="btn btn-sm btn-info" ><icon name="plus"></icon></button>
					</router-link>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Kode Akun</th>
								<th>Perkiraan Akun</th>
								<th>Sub Klasifikasi</th>
								<th>Klasifikasi</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody >
							<tr v-for="(s, index) in queries" :key="s.id">
								<td>{{ s.kode_akun }}</td>
								<td>{{ s.perkiraan_akun }}</td>
								<td>{{ s.sub_klasifikasi }}</td>
								<td>{{ s.klasifikasi }}</td>
                                <td><button type="button" id="send" class="btn btn-sm btn-info" @click="Edit(index)"><icon name="pencil-alt"></icon></button></td>
							</tr>
						</tbody>
					</table>
				</div>
				<pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="perkiraan()"></pagination>
			</div>
		</div>

		<div class="modal fade" id="EditRef" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header modal-header-info">
					<h5 class="modal-title" id="exampleModalLabel">Edit Akun</h5>
				</div>
				<div class="modal-body">
					<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Kode Akun:</label></b-col>
						<b-col sm="12">
							<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="edit_ref.kode_akun"></b-form-input>
						</b-col>
					</b-row>
					<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Nama Akun Perkiraan:</label></b-col>
						<b-col sm="12">
							<b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="edit_ref.perkiraan_akun"></b-form-input>
						</b-col>
					</b-row>
					<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Klasifikasi Akun:</label></b-col>
						<b-col sm="12">
              <b-form-select v-model="edit_ref.sub_klasifikasi">
						    <option :selected="klasifikasi == edit_ref.sub_klasifikasi ? true : false" :value="edit_ref.klasifikasi">
								{{edit_ref.klasifikasi}}
							</option>
							<option value="Harta Tetap">Harta Tetap</option>
							<option value="Harta Lancar">Harta Lancar</option>
							<option value="Kas">Kas</option>
							<option value="Bank">Bank</option>
							<option value="Asset Neto">Asset Neto</option>
							<option value="Pendapatan Tidak Terikat">Pendapatan Tidak Terikat</option>
							<option value="Pendapatan Tidak Terikat Temporer">Pendapatan Terikat Temporer</option>
							<option value="Pendapatan Terikat Permanen">Pendapatan Terikat Permanen</option>
							<option value="Beban Kantor">Beban Kantor</option>
							<option value="Beban Program">Beban Program</option>
							<option value="Beban Titipan">Beban Titipan</option>
							<option value="Beban Wakaf">Beban Wakaf</option>
							<option value="Beban Wakaf">Beban Wakaf</option>
						</b-form-select>
                        </b-col>
					</b-row>
					<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Klasifikasi Akun:</label></b-col>
							<b-col sm="12">
	            	<b-form-select v-model="edit_ref.klasifikasi">
							  		<option :selected="klasifikasi == edit_ref.klasifikasi ? true : false" :value="edit_ref.klasifikasi">
												{{edit_ref.klasifikasi}}
										</option>
										<option value="Harta">Harta</option>
	                  <option value="Kewajiban">Kewajiban</option>
	                  <option value="Modal">Modal</option>
	                  <option value="Beban">Beban</option>
										<option value="Pendapatan">Pendapatan</option>
										<option value="Asset Neto">Asset Neto</option>
									</b-form-select>
	            </b-col>
						</b-row>
            <b-button id="submit" @click="UpdateRef">Update</b-button>
				</div>
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
				last_page: 0,
				queries: [],
        edit_ref: [],
        klasifikasi: '',
				sub_klasifikasi: '',
				pagination: {
					'current_page': 0,
					'total': 0

                },
                dismissSecs: 5,
                dismissCountDown: 0,
				showDismissibleAlert: false,
				items: [{
					text: 'Home',
					to: '/'
				}, {
					text: 'Perkiraan Akun',
					href: '#'
				}]
			}
		  },
		mounted(){
		},
		created: function()
        {
            this.perkiraan();
        },
		methods:{
			perkiraan() {
				axios.get('./list-ref?page=' + this.pagination.current_page)
					.then(response => {
						this.pagination = response.data.pagination
						this.queries	= response.data.data.data
						this.show()
					})
					.catch(error => {
						console.log(error.response.data);
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
			DetailOffer: function(id) {
				$("#DetailOffers").modal("show");
				let uri = './detail-offer/' + id;
                axios.get(uri).then((response) => {
					this.noOffer		= response.data.noOffer
					this.nameattach		= response.data.nameattach
					this.html			= response.data.html
					this.companies		= response.data.Company
					this.brand_name		= response.data.brand_name

                });
			},
			Cetak: function(id){
				alert('nni')
				window.open('./print-pdf-so/' + id, '_blank');
			},
			Edit: function(index){
				$("#EditRef").modal("show");
				this.edit_ref = this.queries[index];
      },
      UpdateRef: function() {
					var id	= this.edit_ref.id;
					document.getElementById("submit").disabled = true;
					let uri = './update-ref/'+ id ;

					 axios.post(uri,{
						kode_akun: this.edit_ref.kode_akun,
						perkiraan_akun: this.edit_ref.perkiraan_akun,
						klasifikasi:  this.edit_ref.klasifikasi,
						sub_klasifikasi: this.edit_ref.sub_klasifikasi
					 }
					 ).then((response) => {
          	$('#submit').removeAttr('disabled');
						this.$swal('Success', 'Your data has been update', 'OK');
						this.sukses	= response.data
            $("#EditRef").modal("hide");
					 });
			},
      countDownChanged (dismissCountDown) {
			  this.dismissCountDown = dismissCountDown
			},
			showAlert () {
			  this.dismissCountDown = this.dismissSecs
			}
		}

    }
</script>
