<template>
	<div id="myContainer">
            <b-breadcrumb :items="items"/>
		<b-card>
			<div class="row">
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'KasMasuk'}">
                    <img :src="'../public/images/finance/1037.bmp'" />
                    </router-link>
                    <p>Kas Masuk</p>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'KasKeluar'}">
                    <img :src="'../public/images/finance/1038.bmp'" />
                    </router-link>
                    <p>Kas Keluar</p>
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
					text: 'Input Transaksi',
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
