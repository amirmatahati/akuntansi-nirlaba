
<script>
    import { Bar  } from 'vue-chartjs';
    export default {
		http: {
            headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
        },
        extends: Bar,
		data(){
			return{
                data_chart: {"forceDecimals": "1"},
                name_chrt:[],
                yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Value'
						}
                    }],
                start:'',
                endMonth: ''
			}
		  },
        mounted() {
            /*
        axios.get('./list-asset').then(response => {
            this.data_chart = response.data.data_chart
            this.name_chrt = response.data.name_chrt
             this.renderChart({
                labels: [this.name_chrt],
                //labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [
                    {
                    label: 'News reports',
                    backgroundColor: '#3c8dbc',
                    data: this.data_chart
                    //data: [12, 20, 12, 18, 10, 6, 9, 32, 29, 19, 12, 11]
                    }
                ]
                },)
        })
        */

   
      axios.get('./list-asset').then(response => {
                        
                       this.data_chart = response.data.data_chart
            this.name_chrt = response.data.name_chrt
            
                
               this.renderChart({
               labels: this.name_chrt,
               datasets: [{
                  label: 'Saldo',
                  backgroundColor: '#FC2525',
                  data: this.data_chart
            }]
         }, {responsive: true, maintainAspectRatio: false})
       
                       
	})
					
                    
   },
   methods:{
			PageSearch: function(){
				alert('ino')
				axios.get('./search-asset?start=' + this.start + '&end='+ this.endMonth).then(response => {
                        
                       this.data_chart = response.data.data_chart
            this.name_chrt = response.data.name_chrt
            
                
               this.renderChart({
               labels: this.name_chrt,
               datasets: [{
                  label: 'Saldo',
                  backgroundColor: '#FC2525',
                  data: this.data_chart
            }]
         }, {responsive: true, maintainAspectRatio: false})
       
                       
					})
			}
		}
		
    }
</script>