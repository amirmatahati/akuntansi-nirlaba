<template>
	<div id="myContainer">
	<div class="page-header">
		<h3 class="page-title">
			Add Akun <icon name="map"></icon>
		</h3>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
				<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Ref'}">Akun Perkiraan</router-link></li>
				<li class="breadcrumb-item active" aria-current="page">Add Akun</li>
			</ol>
		</nav>
	</div>
	<div class="card">
		<div class="card-body">
			<form method="post" v-on:submit.stop.prevent="InsertRef" id="row">
      <b-row class="my-1">
			    <b-col sm="12"><label for="input-small">Kode Akun:</label></b-col>
				<b-col sm="12">
				    <b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="kode_akun"></b-form-input>
				</b-col>
			</b-row>
            <b-row class="my-1">
			    <b-col sm="12"><label for="input-small">Nama Akun Perkiraan:</label></b-col>
				<b-col sm="12">
				    <b-form-input name="no_so" id="input-small" size="sm" type="text" placeholder="Enter" v-model="perkiraan_akun"></b-form-input>
				</b-col>
			</b-row>
			<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Sub Klasifikasi Akun:</label></b-col>
						<b-col sm="12">
							<b-form-select v-model="sub_klasifikasi">
								<option value="Harta Tetap">Harta Tetap</option>
								<option value="Harta Lancar">Harta Lancar</option>
								<option value="Kas">Kas</option>
								<option value="Asset Neto">Asset Neto</option>
								<option value="Bank">Bank</option>
								<option value="Pendapatan Tidak Terikat">Pendapatan Tidak Terikat</option>
								<option value="Pendapatan Tidak Terikat Temporer">Pendapatan Terikat Temporer</option>
								<option value="Pendapatan Terikat Permanen">Pendapatan Terikat Permanen</option>
								<option value="Beban Kantor">Beban</option>
								<option value="Beban Kantor">Beban Kantor</option>
								<option value="Beban Program">Beban Program</option>
								<option value="Beban Titipan">Beban Titipan</option>
								<option value="Beban Wakaf">Beban Wakaf</option>
								<option value="Hutang Usaha">Hutang Usaha</option>
								<option value="Pendapatan">Pendapatan</option>
							</b-form-select>
						</b-col>
					</b-row>
			</b-row>
    	<b-row class="my-1">
						<b-col sm="12"><label for="input-small">Klasifikasi Akun:</label></b-col>
							<b-col sm="12">
	            	<b-form-select v-model="klasifikasi">
									<option value="Harta">Harta</option>
	                <option value="Kewajiban">Kewajiban</option>
	              	<option value="Modal">Modal</option>
	                <option value="Beban">Beban</option>
									<option value="Asset Neto">Asset Neto</option>
									<option value="Pendapatan">Pendapatan</option>
								</b-form-select>
	            </b-col>
					</b-row>
					<br />
					<b-row class="my-1">
						<b-col sm="12">
            		<b-button class="btn btn-gradient-success btn-fw btn-icon-text" id="submit" @click="InsertRef"><i class="mdi mdi-file-check btn-icon-prepend"></i> Submit</b-button>
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
				kode_akun: '',
        perkiraan_akun: '',
				klasifikasi: '',
				sub_klasifikasi: '',
				items: [{
					text: 'Home',
					to: '/'
				}, {
					text: 'Perkiraan Akun',
					to: '/ref'
				},{
					text: 'Add Akun',
					href: '#'
				}]
			}
		  },
		mounted(){
		},
		created: function()
        {

        },
		methods:{
			open() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			show() {
				let loader = this.$loading.show();
				setTimeout(() => loader.hide(), 3 * 1000)
			},
			InsertRef: function() {
					document.getElementById("submit").disabled = true;
					let uri = './insert-ref' ;

					 axios.post(uri,{
						kode_akun: this.kode_akun,
						perkiraan_akun: this.perkiraan_akun,
						klasifikasi:  this.klasifikasi,
						sub_klasifikasi	: this.sub_klasifikasi
					 }
					 ).then((response) => {
						this.$swal('Success', 'Your data has been update', 'OK');
						this.sukses	= response.data
						this.$router.push({name: 'Ref'});
					  });


            }
		}

    }
</script>
