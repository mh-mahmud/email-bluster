<script>
export default {
    mounted(){
    },
    methods: {
        bindCurrentRoute(routeName = ""){ 
            var route = routeName != "" ? routeName :this.$route.name;
            jQuery("#current-route").val(route);
        },
        /**
         * get str/array length
         * @param {*} str 
         */
        getLength(str){
            if(str){
                return str.length;
            }
            
        },

        /**
         * get pagi data
         */
        getPagiData(url,blockUI) {

            var blockuiOpt = {'show':true};
            blockUI =  $.extend(true, blockuiOpt, blockUI); 
            var targetElm = blockUI.target ? blockUI.target :".m-content"; 
            if(blockUI.show == true && typeof commonLib != 'undefined'){
                commonLib.blockUI({target: targetElm, animate: true,overlayColor: 'none', top:'50%'});
            }
            axios.get(url).then((res) => 
            { 
                this.data = res.data;
                if(this.data.title){
                    this.$setDocumentTitle(this.data.title);
                }
                this.pagination =  this.$makePagination(res.data.meta, res.data.links);
                if(blockUI.show == true){
                    commonLib.unblockUI(targetElm);
                }
                
            })
            .catch(function (error) {
                console.log(error.response);
                if(blockUI.show == true){
                    commonLib.unblockUI(targetElm);
                }
            });
        },
        
    },
    filters:{
        formatDate(date, format){
            if (date) {
                return moment(String(date)).format(format)
            }
        },
        /**
        * format int value to HH:MM:SS
        */
        formatHrsMin(s){
            var fm = [
                Math.floor(Math.floor(s/60)/60)%60,   //HOURS
                Math.floor(s/60)%60,  //MINUTES
                s%60    //SECONDS
            ];
            return $.map(fm,function(v,i) { return ( (v < 10) ? '0' : '' ) + v; }).join( ':' );
        }
    }
}
</script>
