<?php

namespace Database\Seeders;

use App\Models\NotesheetTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotesheetTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id') ?? 1;

        $templates = [
            [
                'title' => 'Madrasah Class IX Registration Notesheet',
                'content' => '<p style="text-align:justify">{{ para_1 }}.&nbsp;&nbsp;As per the decision of the Committee of Mutawallis (COM) of IsDB-BISEW in its 73rd meeting, the Program Authority (PA) bears the expenses for class IX registration and Board final exam fees of Dakhil (Vocational) students of {{ madrasah_count }} Madrasahs under IsDB-BISEW Madrasah program.</p><p style="text-align:justify">{{ para_2 }}.&nbsp;&nbsp;The BTEB has initiated the registration process for the academic year {{ session_year }}. And according to the circular of BTEB, dated {{ bteb_circular_date }}, each Dakhil (Voc.) student has to pay Taka {{ per_student_fee }} as a registration fee for enrolling in class IX.</p><p class="indent" style="margin-left:2em; text-align:justify">Meanwhile, {{ madrasah_count }} Madrasahs have completed their online registration process for new Class IX for the academic year {{ session_year }} using BTEB official website www.bteb.gov.bd and submitted the list of students as attached at Flag-A for payment of their registration fee directly to BTEB.</p><p class="indent" style="text-align:justify">The table below shows the number of students completed online registration against the name of the Madrasahs.</p>[DYNAMIC_TABLE]<p class="indent" style="margin-left:2em; text-align:justify">After necessary verification of the list of students by the Program Authority, the above amounts mentioned against the name of the Madrasahs as the BTEB registration fees have been recommended for payment.</p><p style="text-align:justify">{{ para_3 }}.&nbsp;&nbsp;Therefore, approval is sought for payment to the respective Madrasahs as mentioned above. Subject to approval, Cheque(s) may be issued in favour of the above-mentioned Madrasahs from the Current Account No. 20502240100000115 of IsDB-BISEW with Islami Bank Bangladesh PLC, Agargaon Branch, Dhaka, Bangladesh.</p>',
            ],
            [
                'title' => 'Madrasah Class IX Board Exam Notesheet',
                'content' =>
                '<p style="text-align:justify">{{ para_1 }}.&nbsp;&nbsp;The Bangladesh Technical Education Board (BTEB) has initiated the process of board final exam of Class IX for the academic year {{ session_year }} to be held on November {{ session_year }}. According to the circular of BTEB (as attached as Flag-A) dated {{ bteb_circular_date }}, Class IX Dakhil (Vocational) students have to pay Taka {{ per_student_fee }}/student as the board exam fees. Breakdown of fees is given in the table below:</p>'
                    . '<p style="text-align:center"><strong>Table: 1</strong></p>'
                    . '<table style="width:100%;border-collapse:collapse;margin:4px 0;font-size:11pt;">'
                    . '<thead><tr>'
                    . '<th style="border:1px solid #000;padding:3px 5px;text-align:center;width:5%">SL.#</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Item</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;text-align:center;width:8%">Taka</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Remarks</th>'
                    . '</tr></thead>'
                    . '<tbody>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">1</td><td style="border:1px solid #000;padding:3px 5px;">Examination fees</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ bteb_exam_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;">Sonali Bill Payment System to BTEB, from any branch of Sonali Bank that provides Sonali Seba service by {{ exam_payment_deadline }}</td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">2</td><td style="border:1px solid #000;padding:3px 5px;">Number Card fees</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ bteb_number_card_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">3</td><td style="border:1px solid #000;padding:3px 5px;">Industrial Attachment fees (total fees 150 where BTEB 80, Institution 70)</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ bteb_attachment_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td colspan="2" style="border:1px solid #000;padding:3px 5px;text-align:center;"><strong>Sub-Total</strong></td><td style="border:1px solid #000;padding:3px 5px;text-align:right;"><strong>{{ bteb_fee }}</strong></td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td colspan="4" style="border:1px solid #000;padding:3px 5px;"><strong>Centre Fee</strong></td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">4</td><td style="border:1px solid #000;padding:3px 5px;">Centre fees</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ center_main_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;">A/C payee Cheque to Exam Centre by {{ exam_payment_deadline }}</td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">5</td><td style="border:1px solid #000;padding:3px 5px;">Practical Centre fees</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ center_practical_center_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">6</td><td style="border:1px solid #000;padding:3px 5px;">Practical fees (7 Subject &times; 35 taka, per subject 35 taka)</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">{{ center_practical_component_fee }}</td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td colspan="2" style="border:1px solid #000;padding:3px 5px;text-align:center;"><strong>Sub-Total</strong></td><td style="border:1px solid #000;padding:3px 5px;text-align:right;"><strong>{{ center_fee }}</strong></td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td colspan="4" style="border:1px solid #000;padding:3px 5px;"><strong>Institution</strong></td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">7</td><td style="border:1px solid #000;padding:3px 5px;">Industrial Attachment fees (total fees 150 where BTEB 80, Institution 70)</td><td style="border:1px solid #000;padding:3px 5px;text-align:right;">70</td><td style="border:1px solid #000;padding:3px 5px;">A/C payee Cheque to Industrial Attachment by {{ attachment_payment_deadline }}</td></tr>'
                    . '<tr><td colspan="2" style="border:1px solid #000;padding:3px 5px;text-align:center;"><strong>Sub-Total</strong></td><td style="border:1px solid #000;padding:3px 5px;text-align:right;"><strong>{{ attachment_fee }}</strong></td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td colspan="2" style="border:1px solid #000;padding:3px 5px;text-align:center;"><strong>Grand Total</strong></td><td style="border:1px solid #000;padding:3px 5px;text-align:right;"><strong>{{ per_student_fee }}</strong></td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '</tbody></table>'
                    . '<p class="indent" style="margin-left:2em;text-align:justify">The table below shows the number of students submitted online forms against the name of the Madrasahs and payable amounts. Submission of online forms and payable amounts were duly verified and attached at Flag-B.</p>'
                    . '[DYNAMIC_TABLE]'
                    . '<p style="text-align:justify">{{ para_2 }}.&nbsp;&nbsp;Therefore, approval is solicited for payment as follows:</p>'
                    . '<table style="width:100%;border-collapse:collapse;margin:4px 0;font-size:11pt;">'
                    . '<thead><tr>'
                    . '<th style="border:1px solid #000;padding:3px 5px;text-align:center;width:5%">Sl #</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Item</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Payable To</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Amount</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Payment Method</th>'
                    . '<th style="border:1px solid #000;padding:3px 5px;">Payment Duration</th>'
                    . '</tr></thead>'
                    . '<tbody>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">a.</td><td style="border:1px solid #000;padding:3px 5px;">Exam Fees</td><td style="border:1px solid #000;padding:3px 5px;">BTEB</td><td style="border:1px solid #000;padding:3px 5px;">Madrasah wise amount as shown in the table above (Table: 2)</td><td style="border:1px solid #000;padding:3px 5px;">Sonali Bill Payment on account of BTEB favoring concerned Madrasah</td><td style="border:1px solid #000;padding:3px 5px;">To be paid by {{ exam_payment_deadline }}</td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">b.</td><td style="border:1px solid #000;padding:3px 5px;">Exam Centre Fees</td><td style="border:1px solid #000;padding:3px 5px;">Concerned Madrasah</td><td style="border:1px solid #000;padding:3px 5px;">Madrasah wise amount as shown in the table above (Table: 2)</td><td style="border:1px solid #000;padding:3px 5px;">Pay Order favoring each Madrasah</td><td style="border:1px solid #000;padding:3px 5px;"></td></tr>'
                    . '<tr><td style="border:1px solid #000;padding:3px 5px;text-align:center;">c.</td><td style="border:1px solid #000;padding:3px 5px;">Industrial Attachment Fees</td><td style="border:1px solid #000;padding:3px 5px;">Concerned Madrasah</td><td style="border:1px solid #000;padding:3px 5px;">Madrasah wise amount as shown in the table above (Table: 2)</td><td style="border:1px solid #000;padding:3px 5px;"></td><td style="border:1px solid #000;padding:3px 5px;">To be paid before Class IX Dakhil (Vocational) exam tentatively by {{ attachment_payment_deadline }}</td></tr>'
                    . '</tbody></table>',
            ],
            [
                'title' => 'Madrasah Class X Board Exam Notesheet',
                'content' => '<p style="text-align:justify">{{ para_1 }}.&nbsp;&nbsp;As per the decision of the Committee of Mutawallis (COM) of IsDB-BISEW in its 73rd meeting, the Program Authority (PA) bears the expenses for Class X Board final exam fees of Dakhil (Vocational) students of {{ madrasah_count }} Madrasahs under IsDB-BISEW Madrasah program.</p><p style="text-align:justify">{{ para_2 }}.&nbsp;&nbsp;The BTEB has initiated the Board final examination process for Class X Dakhil (Vocational) students for the academic year {{ session_year }}. According to the circular of BTEB, dated {{ bteb_circular_date }}, each Dakhil (Voc.) student has to pay total Taka {{ per_student_fee }} as Board final exam fee comprising of (i) BTEB Board Exam Fee: Taka {{ bteb_fee }}, (ii) Exam Centre Fee: Taka {{ center_fee }} and (iii) Industrial Attachment Fee: Taka {{ attachment_fee }}.</p><p class="indent" style="margin-left:2em; text-align:justify">{{ madrasah_count }} Madrasahs have submitted online exam forms for their Class X Dakhil (Voc.) students for the academic year {{ session_year }} using BTEB official website www.bteb.gov.bd and submitted the list of students as attached at Flag-A for payment of their Board exam fee directly to BTEB.</p><p class="indent" style="margin-left:2em; text-align:justify">The table below shows the number of students submitted online exam forms against the name of the Madrasahs.</p>[DYNAMIC_TABLE]<p class="indent" style="margin-left:2em; text-align:justify">After necessary verification of the list of students by the Program Authority, the above amounts mentioned against the name of the Madrasahs as the BTEB Board final exam fees have been recommended for payment.</p><p style="text-align:justify">{{ para_3 }}.&nbsp;&nbsp;Therefore, approval is sought for payment to the respective Madrasahs as mentioned above. Subject to approval, Cheque(s) may be issued in favour of the above-mentioned Madrasahs from the Current Account No. 20502240100000115 of IsDB-BISEW with Islami Bank Bangladesh PLC, Agargaon Branch, Dhaka, Bangladesh.</p>',
            ],
            [
                'title' => 'Madrasah Teacher Salary Notesheet',
                'content' => '<p><strong>Subject:</strong> Approval for monthly teacher salary disbursement for Academic Session {{ session_year }}</p><p>Salary bill has been prepared as per approved structure and attendance records for the concerned period.</p><p>Total records: {{ total_students }}</p><p>Net payable amount: {{ grand_total }} BDT</p><p>Submitted for kind approval and necessary payment action.</p><p>Date: {{ current_date }}</p>',
            ],
        ];

        foreach ($templates as $template) {
            NotesheetTemplate::query()->updateOrCreate(
                ['title' => $template['title']],
                [
                    'content' => $template['content'],
                    'user_id' => $userId,
                ]
            );
        }
    }
}
