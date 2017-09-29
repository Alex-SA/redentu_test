<template>
    <div>
        <div class="col-md-6">
            <div class="row"><div class="col-md-offset-2"><small>Click for zoom</small></div></div>
            <img :src="'/pictures/' + url" alt="Preview Selected Image" :class="{ full: fullWidthImage }" @click="fullWidthImage = !fullWidthImage">
        </div>
        <div class="col-md-6">
            <label for="image" class="col-md-4 control-label">Picture</label>
            <select name="upload_image" id="upload_image" class="form-control" v-model="url" @click="fullWidthImage = false">
                <option v-for="file in files" :value="file">{{file}}</option>
            </select>

            <div class="alert alert-danger" v-if="error_upload_image">
                <strong>{{ error_upload_image }}</strong>
            </div>

        </div>

    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        props: {
            files_all: '',
            old_selected: '',
            error_upload_image: ''
        },
        data(){
            return{
                url: this.old_selected,
                files: JSON.parse(this.files_all),
                fullWidthImage: false
            }
        }
    }
</script>

<style scoped>
    .full {
        max-width: 1000px;
        max-height: 500px;
        height: auto;

    }
    img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 2px;
        box-shadow: 1px 1px 3px 1px rgba(0, 0, 0, 0.5);
        transition: width 1s;
        z-index: 100;
        position: fixed;
    }
    img:hover {
        cursor: pointer;
    }

</style>