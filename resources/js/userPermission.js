import userPermission from "./components/userPermission.vue"

let app = new Vue({
	el: "#app",
    data: {
        'url': '/getUserPermission',
        'userPermissionUrl': ''
    },
    components: {
        'user-permission': userPermission,
    },
    methods: {
        openUserPermissionModal(company_id, product_id, product_name, start_datetime, end_datetime){
            console.log(start_datetime);
            $('#modalCompanyId').val(company_id);
            $('#modalProductId').val(product_id);
            $('#modalStartDatetime').val(start_datetime);
            $('#modalEndDatetime').val(end_datetime);

            $('.modal-title').text(
                product_name  + '：' + '使用時段' + start_datetime + '-' + end_datetime
            );

            this.userPermissionUrl = this.url + '/' + company_id + '/' + product_id
                + '/' + start_datetime  + '/' + end_datetime

            $('#modal-default').modal('show');
        }
    },
})
