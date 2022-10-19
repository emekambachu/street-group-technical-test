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
                        <button v-if="!loading" class="btn btn-info" type="submit">Import</button>
                        <div v-else>
                            <i class="text-center">Loading</i>
                            <i class="fa fa-spin fa-spinner fa-2x text-center"></i>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Table Records</div>
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
            users: [],
            loading: false,
        }
    },

    methods: {
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
                    if(response.data.success === true){
                        console.log(response.data);
                        this.users = response.data.output;
                    }
                    this.loading = false;
                }).catch((error) => {
                console.log(error);
            });
        },

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

    },

    mounted(){

    }
}
</script>
