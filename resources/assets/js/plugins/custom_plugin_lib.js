const CustomPluginLib = {
    install(Vue, options) {
        Vue.prototype.$printData = (dataset) => {
            console.log(dataset);
        };
        /**
         * delete table data item
         */
        Vue.prototype.$deleteItem = (panel, elem, msg, url) => {
            if (!msg)
                msg = "Are you sure?";
            var href = url;
            var trId = $(elem).parents('tr').attr("id");

            bootbox.confirm(msg, function(result) {
                if (result == true) {
                    commonLib.blockUI({target: ".m-content", animate: true, overlayColor: 'none'});
                    axios.delete(href).then((res) => 
                    {
                        commonLib.iniToastrNotification(res.data.response_msg.type, res.data.response_msg.title, res.data.response_msg.message);
                        commonLib.unblockUI(".m-content");
                        $(elem).parents('tr').remove()
                    })
                    .catch( function(error) {
                        commonLib.iniToastrNotification("warning", "Warning", "Could not delete Item");
                        commonLib.unblockUI(".m-content");
                    }); 
                }
            });
        };
        /**
         * delete data item (universal delete)
         */
        Vue.prototype.$deleteDataItem = (data, index, url, msg, blockUiTarget = ".m-content") => {
            if (!msg)
                msg = "Are you sure?";
            var href = url;

            bootbox.confirm(msg, function(result) {
                if (result == true) {
                    commonLib.blockUI({target: blockUiTarget, animate: true, overlayColor: 'none'});
                    axios.delete(href).then((res) => 
                    {
                        commonLib.iniToastrNotification(res.data.response_msg.type, res.data.response_msg.title, res.data.response_msg.message);
                        commonLib.unblockUI(blockUiTarget);
                        data.splice(index, 1);
                    })
                    .catch( function(error) {
                        commonLib.iniToastrNotification("warning", "Warning", "Could not delete Item");
                        commonLib.unblockUI(blockUiTarget);
                    }); 
                }
            });
        };
        Vue.prototype.$isFileExists = (filePath) =>{
            axios.get('api/file-exists-check?filepath='+filePath).then((res) => 
            { 
                return true;
            })
            .catch(function (error) {
                console.log(error);
                return false;
            });  
        };
        /**
         * get str/array length
         * @param {*} str 
         */
        Vue.prototype.$getLength = (str) =>{
            if(str){
                return str.length;
            }
            
        };
        Vue.prototype.$setDocumentTitle = (title) =>{
            document.title = title;
        };
        /**
         * get sub-string of a string
         */
        Vue.prototype.$getSubString = (str, start, end) =>{ 
            if(str){
                return str.substr(start,end);
            }
            
        };
        /**
         * check privileges
         */
        Vue.prototype.$checkEvPrivilege = (privileges,index) =>{ 
            if(privileges.indexOf(index) != -1 || privileges.indexOf('*') != -1){
                return true;
            }else{
                return false
            }
            
        };
        /**
         * delete pagination items
         * @param {array} data 
         * @param {int} index 
         * @param {array} pagination 
         * @param {string} msg 
         * @param {string} url 
         */
        Vue.prototype.$deletePagiItem = (data, index, pagination, msg, url) => {
            if (!msg)
                msg = "Are you sure?";
            var href = url;

            bootbox.confirm(msg, function(result) {
                if (result == true) {
                    commonLib.blockUI({target: ".m-content", animate: true, overlayColor: 'none', top:'45%'});
                    axios.delete(href).then((res) => 
                    {
                        data.splice(index, 1);
                        pagination.to = pagination.to -1;
                        pagination.total = pagination.total -1;
                        commonLib.iniToastrNotification(res.data.response_msg.type, res.data.response_msg.title, res.data.response_msg.message);
                        commonLib.unblockUI(".m-content");
                        
                    })
                    .catch( function(error) {
                        commonLib.iniToastrNotification("warning", "Warning", "Item Could not delete");
                        commonLib.unblockUI(".m-content");
                    }); 
                }
            });
        };

        /**
         * make pagination data
         */
        Vue.prototype.$makePagination = (meta, links) =>{ 
            return {
                current_page: meta.current_page,
                from: meta.from,
                to: meta.to,
                total: meta.total,
                last_page: meta.last_page,
                next_page_url: links.next,
                prev_page_url: links.prev,
                first_page_url: links.first,
                last_page_url: links.last
                
            };
            
        };
    }
}

export default CustomPluginLib;