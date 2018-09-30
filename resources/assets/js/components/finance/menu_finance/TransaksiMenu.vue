<template>
	<div id="myContainer">
            <b-breadcrumb :items="items"/>
		<b-card>
			<div class="row">
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'JurnalAdd'}">
                    <img :src="'../public/images/finance/1019.bmp'" />
                    </router-link>
                    <p>Transaksi Jurnal Umum</p>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'BukuBesar'}">
                    <img :src="'../public/images/finance/1024.bmp'" />
                    </router-link>
                    <p>Buku Besar</p>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'JurnalList'}">
                    <img :src="'../public/images/finance/1024.bmp'" />
                    </router-link>
                    <p>Daftar Transaksi Jurnal</p>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'Ajp'}">
                    <img :src="'../public/images/finance/ajp.png'" />
                    <p>Ayat Jurnal Penyesuaian</p>
                    </router-link>
                </div>
            </div>
        </b-card>
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
				items: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Transaksi',
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

		},

		methods:{

		}

    }
</script>
