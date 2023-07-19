<template>
    <Head>
        <title>Note Sheet</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Note Sheet Management</PageHeader>
        </template>
        <template #default>
            <div class="flex justify-content-center">
                <div>
                    <InertiaLink :href="route('note_sheet.index')"><button class="btn btn-success">Back</button></InertiaLink>
                    <button v-print="printObj" class="btn btn-success">Print</button>
                    <div id="loading" v-show="printLoading"></div>
                </div>
                <div class="bg-white w-[210mm] min-h-[280mm] my-10 print:my-0 print:mt-10 flex flex-column" id="printMe">
                    <div class="flex flex-row justify-content-between pt-[19mm] print:pt-0 px-[18mm]">
                        <div>Date: {{today}}</div>
                        <div>Page # {{note_sheet.page_no}}</div>
                    </div>
                    <div v-html="note_sheet.note_text" class="px-[15mm] py-8 text-sm">
                    </div>
                    <div class="flex flex-column pt-[10mm] px-[19mm] flex-1">
                        <div class="flex flex-row justify-content-between flex-1 align-items-center">
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Program Officer</div>
                                <div>IsDB-BISEW</div>
                            </div>
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Program Coordinator</div>
                                <div>IsDB-BISEW</div>
                            </div>
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Sr. Program Coordinator</div>
                                <div>IsDB-BISEW</div>
                            </div>
                        </div>
                        <div class="flex flex-row justify-content-between pt-16 flex-1 align-items-center">
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Accounts Officer</div>
                                <div>IsDB-BISEW</div>
                            </div>
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Sr. Accounts Officer</div>
                                <div>IsDB-BISEW</div>
                            </div>
                            <div class="flex flex-column text-center border-t border-black px-3">
                                <div>Chief Executive Officer</div>
                                <div>IsDB-BISEW</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>
<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Label from "@/Components/Label";
import Button from "@/Shared/Button";
import Actions from "@/Shared/Actions";
import NavLink from "@/Components/NavLink";
import moment from "moment";
export default {
    name: "Index",
    props: ['note_sheet', 'can'],
    components: {NavLink, Actions, Button, Label, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            today: moment().format('DD MMMM YYYY'),
            printLoading: true,
            printObj: {
                id: "printMe",
                popTitle: 'good print',
                extraCss: "/css/notesheet_print.css",
                extraHead: '<meta http-equiv="Content-Language" content="zh-cn"/>',
                beforeOpenCallback (vue) {
                    vue.printLoading = true
                },
                openCallback (vue) {
                    vue.printLoading = false
                },
                closeCallback (vue) {
                }
            }
        }
    }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Roboto+Condensed&display=swap');
.table-bordered{
    border: 1px solid gray !important;
}
.table-bordered tr{
    border: 1px solid gray !important;
}
tbody, td, tfoot, th, thead, tr{
    border: 1px solid gray !important;
}
#printMe{
    font-size: 15px;
}
ol li{
    text-align: justify!important;
    font-size: 14px;
    text-justify: inter-word;
}
strong{
    text-decoration: underline;
}
</style>
