<template>
    <div class="w-full">
        <ul>
            <li v-for="folder in folderList.data" @click="getFolderList(folder)" @contextmenu.prevent="contextMenu" :folderId="folder.id">
                <font-awesome-icon
                    icon="folder"
                    size="lg"
                    rotation="rotate"
                    class="text-green-400"
                ></font-awesome-icon>
                {{folder.name}}
            </li>
        </ul>
        <CmpContextMenu :display="showContextMenu" ref="menu">
            <div class="block">
                <div class="bg-white w-60 border border-gray-300 rounded-lg flex flex-col text-sm py-4 px-2 text-gray-500 shadow-lg">
                    <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                        <div class="w-8 text-gray-900">
                            <font-awesome-icon
                                icon="pen"
                                size="lg"
                                rotation="rotate"
                                class="text-green-600"
                            ></font-awesome-icon>
                        </div>
                        <div>Rename</div>
                    </div>
                    <div class="flex hover:bg-gray-100 py-1 px-2 rounded" @click="deleteFolder">
                        <div class="w-8 text-gray-900">
                            <font-awesome-icon
                                icon="trash"
                                size="lg"
                                rotation="rotate"
                                class="text-red-600"
                            ></font-awesome-icon>
                        </div>
                        <div>Delete</div>
                    </div>
                    <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                        <div class="w-8 text-gray-900">
                            <font-awesome-icon
                                icon="copy"
                                size="lg"
                                rotation="rotate"
                                class="text-amber-400"
                            ></font-awesome-icon>
                        </div>
                        <div>Copy</div>
                    </div>
                    <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                        <div class="w-8 text-gray-900">
                            <font-awesome-icon
                                icon="cut"
                                size="lg"
                                rotation="rotate"
                                class="text-red-400"
                            ></font-awesome-icon>
                        </div>
                        <div>Move</div>
                    </div>
                    <hr class="my-3 border-gray-300" />
                    <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                        <div class="w-8 text-gray-900 font-bold">
                            <font-awesome-icon
                                icon="backward"
                                size="lg"
                                rotation="rotate"
                                class="text-gray-600"
                            ></font-awesome-icon>
                        </div>
                        <div>Back</div>
                    </div>
                </div>
            </div>
        </CmpContextMenu>
    </div>
</template>

<script>
import Paginator from "@/Components/Paginator";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo, faFolder, faFolderOpen, faCopy, faCut, faBackward } from "@fortawesome/free-solid-svg-icons";
import CmpContextMenu from "@/Components/Context-menu";
library.add(faPen, faTrash, faInfo, faFolder, faFolderOpen, faCopy, faCut, faBackward);

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@vue-hero-icons/solid'


export default {
    name: "LeftSideBar",
    components: {
        CmpContextMenu, Paginator,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
    },
    props:['getFolder', 'folderList'],
    data(){
        return {
            showContextMenu: false,
            contextFolderId: '',
        }
    },
    methods: {
        getFolderList(base){
            this.$store.dispatch('setActiveFolder', base.id)
            this.$store.dispatch('setActiveFolderInfo', base)
            this.getFolder(base);
        },
        contextMenu(e){
            this.showContextMenu = true
            this.$refs.menu.open(e);
            this.contextFolderId = e.currentTarget.getAttribute('folderId')
        },
        deleteFolder(){
            console.log(this.$store.state.activeFolderInfo)
            this.$inertia.delete(route('delete_folder', this.contextFolderId), { preserveState: true })
            console.log(this.$store.state.activeFolderInfo)
            this.getFolder(this.$store.state.activeFolderInfo);
        }
    }
}
</script>

<style scoped>

</style>
