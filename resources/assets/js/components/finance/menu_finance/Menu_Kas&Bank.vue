<template>
	<div id="myContainer">
		<div class="page-header">
			<h3 class="page-title">
				Transaksi <icon name="map"></icon>
			</h3>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
					<li class="breadcrumb-item active" aria-current="page">Input Transaksi</li>
				</ol>
			</nav>
		</div>
			<div class="row">
				<div class="col-md-2 grid-margin">
				</div>
				<div class="col-md-4 grid-margin">
					<div class="card bg-gradient-info card-img-holder text-white">
						<div class="card-body">
							<img src="admin/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image"/>
							<h4 class="font-weight-normal mb-3">
								<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
							</h4>
							<h2 class="mb-5"><router-link v-bind:to="{name: 'KasMasuk'}" style="color: #fff;">Kas Masuk</router-link></h2>
						</div>
					</div>
				</div>
				<div class="col-md-4 grid-margin">
					<div class="card bg-gradient-success card-img-holder text-white">
						<div class="card-body">
							<img src="admin/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image"/>
							<h4 class="font-weight-normal mb-3">
								<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
							</h4>
							<h2 class="mb-5"><router-link v-bind:to="{name: 'KasKeluar'}" style="color: #fff;">Kas Keluar</router-link></h2>
						</div>
					</div>
				</div>
				<div class="col-md-2 grid-margin">
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
