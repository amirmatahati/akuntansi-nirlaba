<template>
	<div id="myContainer">
	<div class="page-header">
		<h3 class="page-title">
			Buku Besar <icon name="map"></icon>
		</h3>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><router-link v-bind:to="{name: 'Finance'}">Home</router-link></li>
				<li class="breadcrumb-item active" aria-current="page">Buku Besar</li>
			</ol>
		</nav>
	</div>
	<div class="card">
		<b-breadcrumb :items="items"/>
        <div class="card-body">
			<form @submit.prevent="bukubesarData">
                <b-row>
                    <b-col sm="6">
                        <flat-pickr v-model="start" :config="config" placeholder="Select a date"></flat-pickr>
                    </b-col>
                    <b-col sm="6">
                        <div class="input-group mb-3">
                            <flat-pickr v-model="end" :config="config" placeholder="Select end date"></flat-pickr>
                            <div class="input-group-append">
                            <button type="submit" class="btn btn-success btn-xs"><icon name="search"></icon></button>
                            </div>
                        </div>

                    </b-col>

                </b-row>
            </form>


                <h2 class="text-center">Buku besar priode {{ date_a }} - {{ date_b }}</h2>
                <div v-html="bukubesar2">

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
                bukubesar2      : [],
				items			: [{
					text: 'Home',
					to: '/'
				},{
					text: 'Buku Besar',
					href: '#'
                }],
                start           : '',
                end             : '',
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
                date_a          : '',
                date_b          : '',
			}
		  },
		mounted(){


		},
		created: function()
        {

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
			bukubesarData() {
				this.show()
				axios.get('./buku-besar-priode?start='+ this.start + '&end='+ this.end)
					.then(response => {
                        this.bukubesar2	        = response.data.html
                        this.date_a             = response.data.date_a
                        this.date_b             = response.data.date_b


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
