<template>
	<div class="card">
        <h3 class="card-header text-white bg-primary no-margin">Ayat Jurnal Penyesuaian <icon name="map"></icon></h3>
		<b-breadcrumb :items="items"/>
        <div class="card-body">
			<div class="form-group">
					<router-link v-bind:to="{name: 'AdAjp'}">
						<button type="button" class="btn btn-sm btn-info" ><icon name="plus"></icon></button>
					</router-link>
				</div>
            <div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>No. AJP</th>
							<th>Nama Akun</th>
							<th>Debet</th>
							<th>Kredit</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="datas in jurnal" :key="datas.id">
							<td> {{ datas.date_ajp }} </td>
							<td>{{ datas.no_ajp }}</td>
                            <td>{{ datas.perkiraan_akun }}</td>
                            <td>Rp. {{ formatPrice(datas.ajp_debet) }}</td>
                            <td>Rp. {{ formatPrice(datas.ajp_kredit) }}</td>
						</tr>
					</tbody>
				</table>
            </div>
			<pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="jurnalIndex()"></pagination>
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
					text: 'Ayat Jurnal Penyesuaian',
					href: '#'
				}]
			}
		  },
		mounted(){


		},
		created: function()
        {
           this.jurnalIndex()
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
			jurnalIndex() {
				axios.get('./jurnal-penyesuaian?page=' + this.pagination.current_page)
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
				return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
			}
  		}
    }
</script>
