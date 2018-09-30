<template>
	<div id="myContainer">
		<b-card>
			<div class="row">
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'Ref'}">
                    <img :src="'../public/images/finance/data_rekening.jpeg'" />
                    </router-link>
                    <p>Perkiraan Akun</p>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'ListReff'}">
                    <img :src="'../public/images/finance/balance.jpeg'" />
                    <p>Saldo Akun</p>
                    </router-link>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'Mkasbank'}">
                    <img :src="'../public/images/finance/kas_icon.jpg'" />
                    <p>Kas & Bank</p>
                    </router-link>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'TransaksiMenu'}">
                    <img :src="'../public/images/finance/transaksi_icon.png'" />
                    <p>Buku Besar</p>
                    </router-link>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'NeracaSaldo'}">
                    <img :src="'../public/images/finance/neraca.png'" />
                    <p>Neraca Saldo</p>
                    </router-link>
                </div>

                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'LabaRugi'}">
                    <img :src="'../public/images/finance/laba.png'" />
                    <p>Laba Rugi</p>
                    </router-link>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'Equitas'}">
                    <img :src="'../public/images/finance/ekuitas.png'" />
                    <p>Ekuitas</p>
                    </router-link>
                </div>
								<div class="icon_finance">
                    <router-link v-bind:to="{name: 'LaporanAktivitas'}">
                    <img :src="'../public/images/finance/1028.bmp'" />
                    <p>Laporan Aktivitas</p>
                    </router-link>
                </div>
                <div class="icon_finance">
                    <router-link v-bind:to="{name: 'NeracaPenutup'}">
                    <img :src="'../public/images/finance/keuangan.png'" />
                    <p>Neraca Penutup / Posisi Keuangan</p>
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

			}
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
