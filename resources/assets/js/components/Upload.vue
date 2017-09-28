<template>
    <div id="dropzone">
        <div class="title m-b-md">
            Drop it like it's hot!
        </div>

        <dropzone id="myVueDropzone"
                  :url="action"
                  :headers="{ 'X-CSRF-TOKEN': csrf }"
                  :acceptedFileTypes="filetypes"
                  v-on:vdropzone-success="showSuccess">
        </dropzone>

    </div>
</template>

<script>
    import Dropzone from "vue2-dropzone"
    import bus from "../bus"

    export default {
        components: {
            Dropzone
        },
        props: {
            csrf: {
                type: String
            },
            action: {
                type: String
            },
            filetypes: {
                type: String
            }
        },
        methods: {
            showSuccess(file) {
                console.log('File uploaded')
                bus.$emit('uploaded', file)
            }
        }
    }
</script>