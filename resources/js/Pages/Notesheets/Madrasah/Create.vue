<template>
    <Head>
        <title>Create Madrasah Notesheet</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Create Madrasah Notesheet</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <Back :back-url="route('madrasah_notesheet.index')"/>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body">
                        <div class="actions-panel grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="form-label">Note Type</label>
                                <select v-model="form.note_type" class="form-control">
                                    <option value="">Select Note Type</option>
                                    <option value="class_9_registration">Registration</option>
                                    <option value="class_9_exam">Class 9 Exam</option>
                                    <option value="class_10_exam">Class 10 Exam</option>
                                    <option value="teacher_salary">Teacher Salary</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Academic Session</label>
                                <select v-model="form.academic_session" class="form-control">
                                    <option value="">Select Session</option>
                                    <option v-for="session in academic_sessions" :key="session.id" :value="session.session">
                                        {{ session.session }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Template</label>
                                <select v-model="form.template_id" class="form-control">
                                    <option value="">Select Template</option>
                                    <option v-for="template in templates" :key="template.id" :value="template.id">
                                        {{ template.title }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="form.note_type && form.note_type !== 'teacher_salary'">
                                <label class="form-label">BTEB Circular Date</label>
                                <input type="date" v-model="form.bteb_circular_date" class="form-control">
                            </div>
                            <!-- Registration: single BTEB fee input -->
                            <div v-if="form.note_type === 'class_9_registration'">
                                <label class="form-label">BTEB Fee / Student (Taka)</label>
                                <input type="number" v-model.number="form.fee_per_student" class="form-control" min="0" placeholder="e.g. 240">
                            </div>
                            <!-- Exam: separate BTEB, Centre, Attachment fee inputs -->
                            <template v-if="form.note_type === 'class_9_exam' || form.note_type === 'class_10_exam'">
                                <div>
                                    <label class="form-label">BTEB Exam Fee / Student (Taka)</label>
                                    <input type="number" v-model.number="form.fee_per_student" class="form-control" min="0" :placeholder="form.note_type === 'class_9_exam' ? '960' : '1080'">
                                </div>
                                <div>
                                    <label class="form-label">Centre Fee / Student (Taka)</label>
                                    <input type="number" v-model.number="form.center_fee_per_student" class="form-control" min="0" :placeholder="form.note_type === 'class_9_exam' ? '945' : '910'">
                                </div>
                                <div>
                                    <label class="form-label">Attachment Fee / Student (Taka)</label>
                                    <input type="number" v-model.number="form.attachment_fee_per_student" class="form-control" min="0" placeholder="70">
                                </div>
                            </template>
                            <div v-if="form.note_type === 'class_9_exam' || form.note_type === 'class_10_exam'">
                                <label class="form-label">Exam Payment Deadline</label>
                                <input type="date" v-model="form.exam_payment_deadline" class="form-control">
                            </div>
                            <div v-if="form.note_type === 'class_9_exam' || form.note_type === 'class_10_exam'">
                                <label class="form-label">Attachment Payment Deadline</label>
                                <input type="date" v-model="form.attachment_payment_deadline" class="form-control">
                            </div>
                        </div>

                        <div class="actions-panel flex gap-2 mb-4">
                            <button type="button" class="btn btn-success" @click="generatePreview" :disabled="loading">
                                {{ loading ? 'Generating...' : 'Generate Preview' }}
                            </button>
                            <button type="button" class="btn btn-primary" @click="printPage" :disabled="!tableData">
                                Print
                            </button>
                            <button type="button" class="btn btn-info" @click="save" :disabled="!tableData || saving">
                                {{ saving ? 'Saving...' : 'Save Notesheet' }}
                            </button>
                        </div>

                        <div class="notesheet-document" ref="printArea">
                            <div class="page-meta">
                                <div>Date: {{ today }}</div>
                                <div><strong>Page # {{ page_no }}</strong></div>
                            </div>

                            <div class="editor-toolbar actions-panel mb-2">
                                <button type="button" class="btn btn-sm btn-light" @click="formatText('bold')">Bold</button>
                                <button type="button" class="btn btn-sm btn-light" @click="formatText('italic')">Italic</button>
                                <button type="button" class="btn btn-sm btn-light" @click="formatText('underline')">Underline</button>
                            </div>

                            <!-- Text before the dynamic table -->
                            <div
                                class="editor-area"
                                contenteditable="true"
                                @input="onBeforeEditorInput"
                                ref="beforeEditor"
                            ></div>

                            <!-- Dynamic data table -->
                            <div class="table-responsive mt-2" v-if="tableData">

                                <!-- Registration table: SL | Madrasah | Trade | Students | Fee/Student | Total -->
                                <table v-if="columns.is_registration" class="table table-bordered notesheet-table">
                                    <thead>
                                    <tr>
                                        <th>SL.#</th>
                                        <th>Madrasah</th>
                                        <th>Trade</th>
                                        <th># of Student completed Online Registration</th>
                                        <th>BTEB Registration Fee/per student</th>
                                        <th>Total Amount (Taka)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(group, groupIndex) in tableData.madrasah_groups" :key="group.madrasah_name">
                                        <tr v-for="(row, rowIndex) in group.rows" :key="group.madrasah_name + '-' + row.trade_name">
                                            <td v-if="rowIndex === 0" :rowspan="group.rowspan" class="text-center">{{ groupIndex + 1 }}</td>
                                            <td v-if="rowIndex === 0" :rowspan="group.rowspan">{{ group.madrasah_name }}</td>
                                            <td>{{ row.trade_name }}</td>
                                            <td class="text-end">{{ row.student_count }}</td>
                                            <td class="text-end">{{ row.per_student.bteb }}</td>
                                            <td class="text-end">{{ number(row.totals.total) }}</td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <th colspan="5" class="text-end">Total Amount</th>
                                        <th class="text-end">{{ number(tableData.totals.total) }}</th>
                                    </tr>
                                    </tbody>
                                </table>

                                <!-- Exam / salary table: with subtotals per madrasah -->
                                <table v-else class="table table-bordered notesheet-table">
                                    <thead>
                                    <!-- Two-row header for full exam tables (BTEB + Centre + Attachment) -->
                                    <template v-if="columns.show_bteb && columns.show_center && columns.show_attachment">
                                        <tr>
                                            <th rowspan="2" class="text-center">SL.#</th>
                                            <th rowspan="2">Name of the Madrasah</th>
                                            <th rowspan="2">Trade</th>
                                            <th rowspan="2"># of Student completed Online form fill-up</th>
                                            <th colspan="3">Payable to</th>
                                            <th rowspan="2">Total Amount<br>(Taka)</th>
                                        </tr>
                                        <tr>
                                            <th>BTEB as Exam Fee<br>(Taka {{ tableData.fee_structure.bteb }}/regular student)</th>
                                            <th>Madrasah as Exam Centre Fee<br>(Taka {{ tableData.fee_structure.center }}/regular student)</th>
                                            <th>Madrasah as Industrial Attachment Fee<br>(Taka {{ tableData.fee_structure.attachment }}/regular student)</th>
                                        </tr>
                                    </template>
                                    <!-- Single-row header for other types (salary etc.) -->
                                    <tr v-else>
                                        <th>SL</th>
                                        <th>Madrasah Name</th>
                                        <th>Trade</th>
                                        <th>Students</th>
                                        <th v-if="columns.show_bteb">BTEB (TK)</th>
                                        <th v-if="columns.show_center">Exam Center (TK)</th>
                                        <th v-if="columns.show_attachment">Industrial Attachment (TK)</th>
                                        <th>Total (TK)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(group, groupIndex) in tableData.madrasah_groups" :key="group.madrasah_name">
                                        <tr v-for="(row, rowIndex) in group.rows" :key="group.madrasah_name + '-' + row.trade_name">
                                            <td v-if="rowIndex === 0" :rowspan="group.rowspan" class="text-center">{{ groupIndex + 1 }}</td>
                                            <td v-if="rowIndex === 0" :rowspan="group.rowspan">{{ group.madrasah_name }}</td>
                                            <td>{{ row.trade_name }}</td>
                                            <td class="text-end">{{ row.student_count }}</td>
                                            <td v-if="columns.show_bteb" class="text-end">{{ number(row.totals.bteb) }}</td>
                                            <td v-if="columns.show_center" class="text-end">{{ number(row.totals.center) }}</td>
                                            <td v-if="columns.show_attachment" class="text-end">{{ number(row.totals.attachment) }}</td>
                                            <td class="text-end">{{ number(row.totals.total) }}</td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <th :colspan="columnsColspan">Sub Total</th>
                                            <th class="text-end">{{ group.totals.student_count }}</th>
                                            <th v-if="columns.show_bteb" class="text-end">{{ number(group.totals.bteb) }}</th>
                                            <th v-if="columns.show_center" class="text-end">{{ number(group.totals.center) }}</th>
                                            <th v-if="columns.show_attachment" class="text-end">{{ number(group.totals.attachment) }}</th>
                                            <th class="text-end">{{ number(group.totals.total) }}</th>
                                        </tr>
                                    </template>
                                    <tr class="table-dark text-white">
                                        <th :colspan="columnsColspan">Grand Total</th>
                                        <th class="text-end">{{ tableData.total_students }}</th>
                                        <th v-if="columns.show_bteb" class="text-end">{{ number(tableData.totals.bteb) }}</th>
                                        <th v-if="columns.show_center" class="text-end">{{ number(tableData.totals.center) }}</th>
                                        <th v-if="columns.show_attachment" class="text-end">{{ number(tableData.totals.attachment) }}</th>
                                        <th class="text-end">{{ number(tableData.totals.total) }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Text after the dynamic table (when template uses [DYNAMIC_TABLE] marker) -->
                            <div
                                v-if="afterText !== null"
                                class="editor-area mt-2"
                                contenteditable="true"
                                @input="onAfterEditorInput"
                                ref="afterEditor"
                            ></div>

                            <!-- Cheque table: shown only for exam/salary notesheets, not registration notesheets -->
                            <div v-if="chequeRows.length && !columns.is_registration" class="cheque-section mt-4">
                                <p class="mb-2">
                                    Therefore, Cheque from the Current Account may be issued in favour of the Madrasahs listed above
                                    against the number of students mentioned before the name of Madrasahs as follows:
                                </p>
                                <table class="table table-bordered notesheet-table cheque-table">
                                    <thead>
                                    <tr>
                                        <th>SL #</th>
                                        <th>In favor of</th>
                                        <th>Chq. No.</th>
                                        <th>Date</th>
                                        <th>Amount (BDT)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, idx) in chequeRows" :key="item.id">
                                        <td>{{ roman(idx + 1) }}</td>
                                        <td>{{ item.inFavorOf }}</td>
                                        <td>
                                            <input
                                                v-model="item.chqNo"
                                                type="text"
                                                class="cheque-input"
                                                placeholder="Cheque No"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                v-model="item.date"
                                                type="text"
                                                class="cheque-input"
                                                placeholder="DD/MM/YYYY"
                                            >
                                        </td>
                                        <td class="text-end">{{ number(item.amount) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="signatory-section mt-5">
                                <div class="signatory-row">
                                    <div class="signatory-col">
                                        <div class="title">Program Officer</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                    <div class="signatory-col">
                                        <div class="title">Program Coordinator</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                    <div class="signatory-col">
                                        <div class="title">Accounts Officer</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                </div>
                                <div class="signatory-row mt-5">
                                    <div class="signatory-col">
                                        <div class="title">Accounts Officer</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                    <div class="signatory-col">
                                        <div class="title">Sr. Program Coordinator</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                    <div class="signatory-col">
                                        <div class="title">Chief Executive Officer</div>
                                        <div class="org">IsDB-BISEW</div>
                                    </div>
                                </div>
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
import Back from "@/Shared/Back";
import moment from "moment";

export default {
    name: "MadrasahNotesheetCreate",
    props: {
        templates: Array,
        academic_sessions: Array,
        fee_types: Array,
        page_no: Number,
        serial_no: Number,
    },
    components: {Back, CardHeader, PageHeader, Authenticated},
    data() {
        return {
            loading: false,
            saving: false,
            paraStart: null,
            form: {
                note_type: '',
                academic_session: '',
                template_id: '',
                bteb_circular_date: '',
                fee_per_student: null,
                center_fee_per_student: null,
                attachment_fee_per_student: null,
                exam_payment_deadline: '',
                attachment_payment_deadline: '',
            },
            beforeText: '',
            afterText: null,
            tableData: null,
            columns: {
                show_bteb: false,
                show_center: false,
                show_attachment: false,
                is_registration: false,
            },
            chequeRows: [],
            today: moment().format('DD MMMM YYYY'),
        }
    },
    computed: {
        columnsColspan() {
            return 3;
        }
    },
    methods: {
        async generatePreview() {
            if (!this.form.note_type || !this.form.academic_session) {
                alert('Please select note type and academic session first.');
                return;
            }

            this.loading = true;
            try {
                const {data} = await axios.post(route('madrasah_notesheet.preview'), {
                    ...this.form,
                    serial_no: this.serial_no,
                });
                this.tableData = data.table_data;
                this.columns = data.columns;
                if (data.para_start) this.paraStart = data.para_start;

                const parsedText = data.parsed_text;
                if (parsedText.includes('[DYNAMIC_TABLE]')) {
                    const parts = parsedText.split('[DYNAMIC_TABLE]');
                    this.beforeText = parts[0];
                    this.afterText = parts[1] || '';
                } else {
                    this.beforeText = parsedText;
                    this.afterText = null;
                }

                this.$nextTick(() => {
                    if (this.$refs.beforeEditor) this.$refs.beforeEditor.innerHTML = this.beforeText;
                    if (this.$refs.afterEditor) this.$refs.afterEditor.innerHTML = this.afterText || '';
                });

                this.buildChequeRows();
            } finally {
                this.loading = false;
            }
        },
        save() {
            this.saving = true;
            const noteText = this.afterText !== null
                ? this.beforeText + '[DYNAMIC_TABLE]' + this.afterText
                : this.beforeText;
            this.$inertia.post(route('madrasah_notesheet.store'), {
                note_text: noteText,
                page_no: this.page_no,
                serial_no: this.paraStart ?? this.serial_no,
                note_type: this.form.note_type,
                academic_session: this.form.academic_session,
                meta_data: {
                    academic_session: this.form.academic_session,
                    note_type: this.form.note_type,
                    total_students: this.tableData?.total_students || 0,
                    fee_structure: this.tableData?.fee_structure || {},
                    table_data: this.tableData,
                    cheque_rows: this.chequeRows,
                },
            }, {
                onFinish: () => {
                    this.saving = false;
                }
            });
        },
        printPage() {
            const printContent = this.$refs.printArea?.innerHTML;
            if (!printContent) {
                alert('Nothing to print. Generate the notesheet first.');
                return;
            }

            const printWindow = window.open('', '_blank', 'width=900,height=1200');
            if (!printWindow) {
                alert('Print popup blocked. Please allow popups for this site.');
                return;
            }

            const style = `
                <style>
                    @page { size: A4; margin: 16mm 14mm 18mm 14mm; }
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body {
                        font-family: "Times New Roman", Times, serif;
                        font-size: 11pt;
                        margin: 0;
                        padding: 0;
                        color: #000;
                        line-height: 1.5;
                    }
                    [contenteditable] {
                        outline: none;
                        border: none !important;
                        padding: 0 !important;
                    }
                    .page-meta { display: flex; justify-content: space-between; font-size: 11pt; margin-bottom: 10px; }
                    .editor-area {
                        font-size: 11pt;
                        line-height: 1.5;
                        text-align: justify;
                        border: none !important;
                        padding: 0 !important;
                    }
                    .editor-area p {
                        margin: 0 0 6pt 0;
                        text-align: justify;
                        font-size: 11pt;
                        line-height: 1.5;
                    }
                    p.indent,
                    .editor-area p.indent {
                        margin-left: 2em !important;
                    }
                    .cheque-section p { font-size: 11pt; line-height: 1.5; text-align: justify; }
                    .notesheet-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                    .notesheet-table th, .notesheet-table td {
                        border: 1px solid #111;
                        font-size: 11pt;
                        line-height: 1.3;
                        padding: 3px 5px;
                        vertical-align: middle;
                    }
                    .notesheet-table th { text-align: center; font-weight: 700; }
                    .text-end { text-align: right; }
                    .text-center { text-align: center; }
                    .signatory-section { margin-top: 50px; }
                    .signatory-row { display: flex; justify-content: space-between; gap: 2rem; margin-top: 60px; }
                    .signatory-col {
                        flex: 1;
                        text-align: center;
                        border-top: 1px solid #333;
                        padding-top: 4px;
                        font-size: 11pt;
                    }
                    .signatory-col .title { font-weight: 600; }
                    .signatory-col .org { font-size: 10pt; }
                    .actions-panel { display: none !important; }
                    .editor-toolbar { display: none !important; }
                    .cheque-input {
                        width: 100%;
                        border: none;
                        outline: none;
                        font-family: "Times New Roman", Times, serif;
                        font-size: 11pt;
                        background: transparent;
                    }
                    .table-responsive { overflow: visible; }
                </style>
            `;

            printWindow.document.write(`<html><head><title>Madrasah Notesheet</title>${style}</head><body>${printContent}</body></html>`);
            printWindow.document.close();
            printWindow.focus();

            const runPrint = () => {
                printWindow.print();
            };

            if ('onafterprint' in printWindow) {
                printWindow.onafterprint = () => {
                    printWindow.close();
                };
                runPrint();
            } else {
                setTimeout(() => {
                    runPrint();
                    printWindow.close();
                }, 250);
            }
        },
        buildChequeRows() {
            if (!this.tableData?.madrasah_groups) {
                this.chequeRows = [];
                return;
            }

            this.chequeRows = this.tableData.madrasah_groups.map((group, idx) => ({
                id: `${idx + 1}-${group.madrasah_name}`,
                inFavorOf: group.madrasah_name,
                chqNo: '',
                date: moment().format('DD/MM/YYYY'),
                amount: group.totals.total || 0,
            }));
        },
        onBeforeEditorInput(event) {
            this.beforeText = event.target.innerHTML;
        },
        onAfterEditorInput(event) {
            this.afterText = event.target.innerHTML;
        },
        formatText(command) {
            document.execCommand(command, false, null);
        },
        number(value) {
            return new Intl.NumberFormat('en-BD').format(value || 0);
        },
        roman(num) {
            const map = [
                ['', 'i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix'],
                ['', 'x', 'xx', 'xxx', 'xl', 'l', 'lx', 'lxx', 'lxxx', 'xc'],
            ];
            const tens = Math.floor(num / 10);
            const ones = num % 10;
            return `${map[1][tens] || ''}${map[0][ones] || ''}`;
        }
    }
}
</script>

<style scoped>
.editor-area {
    min-height: 180px;
    border: 1px solid #cfd6dd;
    border-radius: 6px;
    padding: 12px 14px;
    background: #fff;
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    line-height: 1.5;
    text-align: justify;
}

.editor-area p {
    margin: 0 0 6pt 0;
    text-align: justify;
}

.editor-area p.indent,
.editor-area >>> p.indent {
    margin-left: 2em;
}

.notesheet-document {
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 18px 20px;
}

.page-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
}

.editor-toolbar {
    display: flex;
    gap: 8px;
}

.notesheet-table th,
.notesheet-table td {
    vertical-align: middle;
    border: 1px solid #111 !important;
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    line-height: 1.2;
    padding: 3px 5px;
}

.notesheet-table th {
    text-align: center;
    font-weight: 700;
}

.cheque-section p {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    line-height: 1.4;
    text-align: justify;
}

.cheque-input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    padding: 0;
}

.signatory-row {
    display: flex;
    justify-content: space-between;
    gap: 2rem;
    margin-top: 50px;
}

.signatory-col {
    flex: 1;
    text-align: center;
    border-top: 1px solid #333;
    padding-top: 4px;
    min-height: 50px;
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
}

.signatory-col .title {
    font-weight: 600;
}

.signatory-col .org {
    font-size: 10pt;
}

@media print {
    @page {
        size: A4;
        margin: 16mm 14mm 18mm 14mm;
    }

    html,
    body {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-family: "Times New Roman", Times, serif;
    }

    .actions-panel,
    .navbar,
    .main-sidebar,
    .app-sidebar,
    .card-header,
    .card-footer,
    .main-footer,
    .content-header {
        display: none !important;
    }

    .container-fluid,
    .card,
    .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .notesheet-document {
        border: none !important;
        padding: 0 !important;
    }

    .editor-area {
        border: none !important;
        padding: 0 !important;
        min-height: auto !important;
    }

    .notesheet-table tr,
    .signatory-section,
    .signatory-row {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .cheque-input {
        border: none !important;
        -webkit-appearance: none;
        appearance: none;
    }
}
</style>
