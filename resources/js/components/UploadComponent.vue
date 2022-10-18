<template>

    <div class="row justify-content-center mt-5">
        <div class="col-12">
            <h3>Import CSV</h3>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload CSV</div>

                <div class="card-body text-center">
                    <form @submit.prevent="importFile">
                        <input type="file" @change="uploadFile" class="form-control" required>
                        <div v-if="fileErrorMessage === '' && form.file !== null">
                            <i>{{ filePreview }}</i>
                            <span @click="form.file = null"
                                  class="ml-2 fa fa-times text-danger"
                                  title="Remove image"></span>
                        </div>
                        <button class="btn btn-info" type="submit">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">Records</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Initial</th>
                                <th scope="col">Last Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <th scope="row">{{ index }}</th>
                                <td>{{ user.title }}</td>
                                <td>{{ user.first_name }}</td>
                                <td>{{ user.initial }}</td>
                                <td>{{ user.last_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</template>

<script>
export default {
    data(){
        return {
            form: {
                file: null,
            },
            filePreview: null,
            validationAlert: '',
            fileErrorMessage: '',
            errors: [],
            loading: false,
            users: []
        }
    },

    methods: {
        uploadFile: function (event){
            this.validateFile(event);
            //Assign image and path to this variable
            this.form.file = event.target.files[0];
            this.fileErrorMessage = '';
            // create base64 version display of image
            this.filePreview = event.target.files[0].name;
        },

        validateFile(event){
            // Validate Image
            let allowedExtensions = /(\.csv|\.xls|\.xlsx)$/i;
            if(!allowedExtensions.exec(event.target.files[0].name)){
                this.fileErrorMessage = 'Incorrect format';
                this.form.file = null;
                return false;
            }
        },

        importFile(){
            this.loading = true;
            this.errors = [];
            let formData = new FormData();
            // iterate form object and append to formData
            let self = this;
            Object.keys(this.form).forEach(function(key) {
                formData.append(key, self.form[key]);
            });
            // check entries
            console.log(...formData.entries());
            axios.post('/api/users/import/csv', formData)
                .then((response) => {
                    console.log(response.data);
                    if(response.data.success === true){
                        console.log('Completed')
                        this.formEmpty();
                        this.getUsers();
                    }else{
                        this.formError(response);
                    }
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                });
        },

        getUsers(){
            axios.get('/api/users')
                .then((response) => {
                    console.log(response.data);
                    if(response.data.success === true){
                        this.users = response.data.users;
                        console.log(response.data.users);
                    }else{
                        console.log(response.data.message);
                    }
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                });
        },

        formError(response){
            Swal.fire({
                icon: 'error',
                title: 'Error Occurred',
                showConfirmButton: false,
                timer: 2500
            });
            this.errors = response.data.errors;
            console.log(this.errors);
            console.log(response.data.message);
        },

        formEmpty(){
            let self = this; //you need this because *this* will refer to Object.keys below`
            //Iterate through each object field, key is name of the object field`
            Object.keys(this.form).forEach(function(value) {
                self.form[value] = null;
            });
        },

    },

    mounted(){
        this.getUsers();
    }
}
</script>
