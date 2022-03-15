<template>
    <Head>
        <title>File Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>File Management</h2>
            </PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="flex flex-col sm:flex-row">
                        <div>
                            <span class="hover:cursor-pointer text-blue-900 px-1" @click="getFolder(false)">.../</span>
                            <span v-if="$store.state.activeFolderInfo?.parent" class="hover:cursor-pointer text-blue-900 px-1" @click="getFolder($store.state.activeFolderInfo?.parent)">{{$store.state.activeFolderInfo?.parent?.name}}/</span>
                            <span v-if="$store.state.activeFolderInfo" class="hover:cursor-pointer text-blue-900 px-1" @click="getFolder($store.state.activeFolderInfo)">{{$store.state.activeFolderInfo.name}}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" @dragover.prevent @drop.prevent>
                    <div class="flex flex-col sm:flex-row">
                        <div class="w-full sm:w-1/5 sm:border-r-2">
                            <LeftSideBar :getFolder="getFolder" :folderList="folder_list"/>
                        </div>
                        <div class="w-full sm:w-4/5 min-h-screen" @drop.prevent="dropUpload" draggable></div>
                    </div>
                    <div class="flex flex-col w-12 fixed right-5 bottom-1 sm:bottom-8 max-h-fit rounded z-50 justify-center items-center text-center align-middle">
                        <div class="shadow p-1 text-center align-middle cursor-pointer">
                            <font-awesome-icon
                                icon="file-upload"
                                size="lg"
                                rotation="rotate"
                                class="text-green-600"
                            ></font-awesome-icon>
                        </div>
                        <div class="shadow p-1 text-center align-middle cursor-pointer" @click="openCreateFolder()">
                            <font-awesome-icon
                                icon="folder-plus"
                                size="lg"
                                rotation="rotate"
                                class="text-green-600"
                            ></font-awesome-icon>
                        </div>
                    </div>
                </div>
            </div>
            <Modal :openModal="createFolder" :closeModal="closeModal" class="w-full">
                <template #header>
                    Create New Folder
                </template>
                <template #body>
                    <form action="" @submit.prevent="createFolderPost">
                        <div class="form-group">
                            <label for="folder_name">Folder Name</label>
                            <input type="text" id="folder_name" class="form-control focus:outline-amber-800" v-model="createFolderForm.name">
                        </div>
                        <div class="form-group flex justify-end">
                            <input type="submit" class="bg-green-600 rounded mt-2 px-2 pull-right text-white">
                        </div>
                    </form>
                </template>
                <template #footer></template>
            </Modal>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import LeftSideBar from "@/Pages/BillAttachment/inc/LeftSideBar";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo, faFolder, faFolderOpen, faAngleDoubleUp, faAngleDoubleDown, faFolderPlus, faFileUpload} from "@fortawesome/free-solid-svg-icons";
import Modal from "@/Shared/Modal";
library.add(faPen, faTrash, faInfo, faFolder, faFolderOpen, faAngleDoubleUp, faAngleDoubleDown, faFolderPlus, faFileUpload);

export default {
    name: "Index",
    components: {Modal, LeftSideBar, PageHeader, Authenticated},
    data(){
        return {
            folder_list: {},
            activeFolderInfo: this.$store.state.activeFolderInfo,
            createFolder: false,
            createFolderForm: {
                name: ''
            },
            fileFolder: [],
            jsFormData: new FormData(),
        }
    },
    methods: {
        getFolder(base){
            this.$store.dispatch('setActiveFolder', base?.id)
            this.$store.dispatch('setActiveFolderInfo', base)
            fetch(route('folder_list', base))
            .then(data => data.json())
            .then((data) => {
                this.folder_list = data
            })
        },
        openCreateFolder(){
            this.createFolder = !this.createFolder
        },
        closeModal(){
            this.createFolder = false
        },
        createFolderPost(){
            this.$inertia.post(route('create_folder'), {
                name: this.createFolderForm.name,
                parent_id: this.$store.state.activeFolder
            }, {
                preserveState: (page) => console.log(page)
            })
            this.getFolder(this.$store.state.activeFolderInfo)
        },
        traverseFileTree(item, path) {
            path = path || "";
            const app = this
            if (item.isFile) {
                // Get file
                item.file(function(file) {
                    app.fileFolder.push(file)
                });
            } else if (item.isDirectory) {
                // Get folder contents
                const dirReader = item.createReader();
                dirReader.readEntries(function(entries) {
                    for (let i=0; i<entries.length; i++) {
                        app.traverseFileTree(entries[i], path + item.name + "/");
                    }
                });
            }
        },
        dropUpload(e){
            const items = e.dataTransfer.items;
            // console.log(e.dataTransfer.items)
            var data = []
            let formData = new FormData();
            for (let i=0; i<items.length; i++) {
                // webkitGetAsEntry is where the magic happens
                const item = items[i].webkitGetAsEntry();
                // console.log(item.isFile)
                // console.log(item)
                if (item) {
                    // data.push(this.traverseFileTree(item)) ;
                    this.traverseFileTree(item)
                }
            }
            // this.$inertia.post(route('upload_file'), this.jsFormData)
        }
    },
    mounted() {
        let base = this.$store.state.activeFolder
        this.getFolder(base)
    }
}
</script>

<style scoped>

</style>
