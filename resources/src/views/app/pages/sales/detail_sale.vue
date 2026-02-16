<template>
  <div class="main-content">
    <breadcumb :page="$t('SaleDetail')" :folder="$t('Sales')"/>
    <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>

    <b-card v-if="!isLoading">
      <b-row>
        <b-col md="12" class="mb-5">

          <router-link
            v-if="currentUserPermissions && currentUserPermissions.includes('Sales_edit') && sale.sale_has_return == 'no'"
            title="Edit"
            class="btn btn-success btn-icon ripple btn-sm"
            :to="{ name:'edit_sale', params: { id: $route.params.id } }"
          >
            <i class="i-Edit"></i>
            <span>{{$t('EditSale')}}</span>
          </router-link>

          <button @click="Send_Email()" class="btn btn-info btn-icon ripple btn-sm">
            <i class="i-Envelope-2"></i>
            {{$t('Email')}}
          </button>
           <button @click="Sale_SMS()" class="btn btn-secondary btn-icon ripple btn-sm">
            <i class="i-Speach-Bubble"></i>
            SMS
          </button>
          <button @click="Sale_PDF()" class="btn btn-primary btn-icon ripple btn-sm">
            <i class="i-File-TXT"></i>
            PDF
          </button>
          <button @click="printPDF()" class="btn btn-primary btn-icon ripple btn-sm">
  <i class="i-File-TXT"></i>
  Print PDF
</button>
          <button @click="print()" class="btn btn-warning btn-icon ripple btn-sm">
            <i class="i-Billing"></i>
            {{$t('print')}}
          </button>
          <button
            v-if="currentUserPermissions && currentUserPermissions.includes('Sales_delete') && sale.sale_has_return == 'no'"
            @click="Delete_Sale()"
            class="btn btn-danger btn-icon ripple btn-sm"
          >
            <i class="i-Close-Window"></i>
            {{$t('Del')}}
          </button>
        </b-col>
      </b-row>
      <div class="invoice" id="print_Invoice">
        <div class="invoice-print">

          <!-- ── COMPANY TITLE ── -->
          <div class="inv-title-row">
            <h4 class="inv-title">{{ company.CompanyName }}</h4>
            <div class="inv-title">DSP Chowk, Ghalwa Road Alipur.</div>
          </div>

          <hr class="inv-hr" />

          <!-- ── SALE DETAIL HEADING ── -->
          <!-- <b-row class="justify-content-md-center">
            <h4 class="font-weight-bold">{{ $t('SaleDetail') }} : {{ sale.date }}</h4>
          </b-row> -->

          <hr />

          <!-- ── 3 COLUMN INFO ROW ── -->
          <b-row class="mt-5">

            <!-- Customer Info -->
            <b-col lg="3" md="3" sm="12" class="mb-4">
              <h5 class="info-heading">{{ $t('Customer_Info') }}</h5>
              <div>{{ sale.client_name }}</div>
              <div>{{ sale.client_email }}</div>
              <div>{{ sale.client_phone }}</div>
              <div>{{ sale.client_adr }}</div>
            </b-col>

            <!-- Company Info -->
            <b-col lg="3" md="3" sm="12" class="mb-4">
              <h5 class="info-heading">Bill.No</h5>
              <div>{{ sale.Ref }}</div>
      
            </b-col>

            <b-col lg="2" md="2" sm="12" class="mb-4">
              <h5 class="info-heading">Date</h5>
              <div>{{ sale.date }}</div>
    
            </b-col>
            <!-- Invoice Info -->
            <b-col lg="4" md="4" sm="12" class="mb-4">
              <h5 class="info-heading">{{ $t('Invoice_Info') }}</h5>
              <!-- <div>{{ $t('Reference') }} : {{ sale.Ref }}</div> -->
              <div>
                {{ $t('PaymentStatus') }} :
                <span v-if="sale.payment_status == 'paid'" class="badge badge-outline-success">{{ $t('Paid') }}</span>
                <span v-else-if="sale.payment_status == 'partial'" class="badge badge-outline-primary">{{ $t('partial') }}</span>
                <span v-else class="badge badge-outline-warning">{{ $t('Unpaid') }}</span>
              </div>
              <!-- <div>{{ $t('warehouse') }} : {{ sale.date  }}</div> -->
              <div>
                {{ $t('Status') }} :
                <span v-if="sale.statut == 'completed'" class="badge badge-outline-success">{{ $t('complete') }}</span>
                <span v-else-if="sale.statut == 'pending'" class="badge badge-outline-info">{{ $t('Pending') }}</span>
                <span v-else class="badge badge-outline-warning">{{ $t('Ordered') }}</span>
              </div>
            </b-col>

          </b-row>

          <!-- ── ORDER SUMMARY ── -->
          <b-row class="mt-3">
            <b-col md="12">
              <h5 class="info-heading">{{ $t('Order_Summary') }}</h5>
              <div class="table-responsive">
                <table class="table table-hover table-md">
                  <thead class="bg-gray-300">
                    <tr>
                      <th scope="col">{{ $t('ProductName') }}</th>
                      <th scope="col">{{ $t('UnitPrice') }}</th>

                      <th scope="col">{{ $t('Quantity') }}</th>
                      <th scope="col">{{ $t('Net_Unit_Price') }}</th>

                      <th scope="col">{{ $t('Discount') }}</th>
                      <th scope="col">{{ $t('Tax') }}</th>
                      <th scope="col">{{ $t('SubTotal') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="detail in details" :key="detail.code">
                      <td>
                        <!-- <span>{{ detail.code }} ({{ detail.name }})</span> -->
                        <span>{{ detail.name }}</span>

                        <p v-show="detail.is_imei && detail.imei_number !== null" class="imei-text">
                          {{ $t('IMEI_SN') }} : {{ detail.imei_number }}
                        </p>
                      </td>
                      <td>{{ currentUser.currency }} {{ formatNumber(detail.price, 2) }}</td>
                      <td>{{ formatNumber(detail.quantity, 2) }} {{ detail.unit_sale }}</td>
                      <td>{{ currentUser.currency }} {{ formatNumber(detail.Net_price, 3) }}</td>


                      <td>{{ currentUser.currency }} {{ formatNumber(detail.DiscountNet, 2) }}</td>
                      <td>{{ currentUser.currency }} {{ formatNumber(detail.taxe, 2) }}</td>
                      <td>{{ currentUser.currency }} {{ detail.total.toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </b-col>

            <!-- ── TOTALS ── -->
            <div class="offset-md-9 col-md-3 mt-4">
              <table class="table table-striped table-sm">
                <tbody>
                  <tr>
                    <td>{{ $t('OrderTax') }}</td>
                    <td>
                      <span>{{ currentUser.currency }} {{ sale.TaxNet.toFixed(2) }} ({{ formatNumber(sale.tax_rate, 2) }} %)</span>
                    </td>
                  </tr>
                  <tr>
                    <td>{{ $t('Discount') }}</td>
                    <td>{{ currentUser.currency }} {{ sale.discount.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <td>{{ $t('Shipping') }}</td>
                    <td>{{ currentUser.currency }} {{ sale.shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <td><span class="font-weight-bold">{{ $t('Total') }}</span></td>
                    <td><span class="font-weight-bold">{{ currentUser.currency }} {{ sale.GrandTotal }}</span></td>
                  </tr>
                  <tr>
                    <td><span class="font-weight-bold">{{ $t('Paid') }}</span></td>
                    <td><span class="font-weight-bold">{{ currentUser.currency }} {{ sale.paid_amount }}</span></td>
                  </tr>
                  <tr>
                    <td><span class="font-weight-bold">{{ $t('Due') }}</span></td>
                    <td><span class="font-weight-bold">{{ currentUser.currency }} {{ sale.due }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>

          </b-row>

          <!-- ── NOTE ── -->
          <hr v-show="sale.note" />
          <b-row class="mt-4">
            <b-col md="12">
              <p>{{ $t('sale_note') }} : {{ sale.note }}</p>
            </b-col>
          </b-row>

        </div><!-- end .invoice-print -->
      </div><!-- end #print_Invoice -->

    </b-card>
  </div>
   
</template>

<script>

import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";
import html2pdf from "html2pdf.js";

export default {
  computed: mapGetters(["currentUserPermissions", "currentUser"]),
  metaInfo: {
    title: "Detail Sale"
  },

  data() {
    return {
      isLoading: true,
      sale: {},
      details: [],
      variants: [],
      company: {},
      email: {
        to: "",
        subject: "",
        message: "",
        client_name: "",
        Sale_Ref: ""
      }
    };
  },

  methods: {
   

    //----------------------------------- Invoice Sale PDF  -------------------------\\
    Sale_PDF() {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      let id = this.$route.params.id;
     
       axios
        .get(`sale_pdf/${id}`, {
          responseType: "blob", // important
          headers: {
            "Content-Type": "application/json"
          }
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "Sale_" + this.sale.Ref + ".pdf");
          document.body.appendChild(link);
          link.click();
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        })
        .catch(() => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        });
    },

     //------ Toast
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },

    //------------------------------Formetted Numbers -------------------------\\
    formatNumber(number, dec) {
      const value = (typeof number === "string"
        ? number
        : number.toString()
      ).split(".");
      if (dec <= 0) return value[0];
      let formated = value[1] || "";
      if (formated.length > dec)
        return `${value[0]}.${formated.substr(0, dec)}`;
      while (formated.length < dec) formated += "0";
      return `${value[0]}.${formated}`;
    },

    //------------------------------ Print -------------------------\\
    print() {
      this.$htmlToPaper('print_Invoice');
    },

    printPDF() {
  const element = document.getElementById('print_Invoice');

  const options = {
    margin:       0.2,                  // page margin
    filename:     'Invoice_' + this.sale.Ref + '.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2, useCORS: true },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(options).from(element).save();
},

    Send_Email() {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      let id = this.$route.params.id;
      axios
        .post("sales_send_email", {
          id: id,
        })
        .then(response => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
          this.makeToast(
            "success",
            this.$t("Send.TitleEmail"),
            this.$t("Success")
          );
        })
        .catch(error => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
          this.makeToast("danger", this.$t("SMTPIncorrect"), this.$t("Failed"));
        });
    },

    //---------SMS notification
     Sale_SMS() {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      let id = this.$route.params.id;
      axios
        .post("sales_send_sms", {
          id: id,
        })
        .then(response => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
          this.makeToast(
            "success",
            this.$t("Send_SMS"),
            this.$t("Success")
          );
        })
        .catch(error => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
          this.makeToast("danger", this.$t("sms_config_invalid"), this.$t("Failed"));
        });
    },

    //----------------------------------- Get Details Sale ------------------------------\\
    Get_Details() {
      let id = this.$route.params.id;
      axios
        .get(`sales/${id}`)
        .then(response => {
          this.sale = response.data.sale;
          this.details = response.data.details;
          this.company = response.data.company;
          this.isLoading = false;
        })
        .catch(response => {
          setTimeout(() => {
            this.isLoading = false;
          }, 500);
        });
    },

    //------------------------------------------ DELETE Sale ------------------------------\\
    Delete_Sale() {
      let id = this.$route.params.id;
      this.$swal({
        title: this.$t("Delete.Title"),
        text: this.$t("Delete.Text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: this.$t("Delete.cancelButtonText"),
        confirmButtonText: this.$t("Delete.confirmButtonText")
      }).then(result => {
        if (result.value) {
          axios
            .delete("sales/" + id)
            .then(() => {
              this.$swal(
                this.$t("Delete.Deleted"),
                this.$t("Delete.SaleDeleted"),
                "success"
              );
              this.$router.push({ name: "index_sales" });
            })
            .catch(() => {
              this.$swal(
                this.$t("Delete.Failed"),
                this.$t("Delete.Therewassomethingwronge"),
                "warning"
              );
            });
        }
      });
    }
  }, //end Methods

  //----------------------------- Created function-------------------

  created: function() {
    this.Get_Details();
  }
};
</script>
<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@300;400;700&family=IBM+Plex+Mono:wght@400;500&display=swap');

:root {
  --ink:     #1a1a2e;
  --accent:  #2563b0;
  --paper:   #fdfaf4;
  --border:  #d1c9b5;
  --muted:   #6b6350;
  --success: #15803d;
  --warning: #b45309;
  --info:    #0369a1;
}

/* ── INVOICE CARD ── */
.invoice {
  background: var(--paper);
  border: 1px solid var(--border);
  box-shadow: 0 6px 48px rgba(0, 0, 0, 0.13);
  border-top: 5px solid var(--accent);
}

.invoice-print {
  padding: 36px 40px 30px;
}

/* ── COMPANY TITLE ── */
.inv-title-row {
  text-align: center;
  margin-bottom: 4px;
}

.inv-title {
  font-family: 'Playfair Display', serif;
  font-size: 30px;
  font-weight: 700;
  color: var(--accent);
  letter-spacing: 0.05em;
  text-transform: uppercase;
  text-align: center;
}

.inv-sub {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  color: var(--muted);
  letter-spacing: 0.1em;
  margin-top: 4px;
  text-align: center;
}

.inv-hr {
  border: none;
  border-top: 1.5px solid var(--border);
  margin: 14px 0;
}

/* ── INFO HEADINGS ── */
.info-heading {
  font-size: 14px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 10px;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--accent);
  letter-spacing: 0.02em;
}

/* ── BADGES ── */
.badge {
  display: inline-block;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.06em;
  padding: 2px 8px;
  border-radius: 3px;
  border: 1px solid transparent;
}

.badge-outline-success { background: #dcfce7; color: var(--success); border-color: #86efac; }
.badge-outline-primary { background: #dbeafe; color: var(--accent);  border-color: #93c5fd; }
.badge-outline-warning { background: #fef3c7; color: var(--warning); border-color: #fcd34d; }
.badge-outline-info    { background: #e0f2fe; color: var(--info);    border-color: #7dd3fc; }

/* ── TABLE ── */
.table thead th {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.imei-text {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  color: var(--muted);
  margin-top: 4px;
  margin-bottom: 0;
}

/* ── PRINT MEDIA ──
   $htmlToPaper only sends #print_Invoice content to the printer,
   so no extra @media print rules are needed here.
   The action buttons are outside #print_Invoice and never printed.
── */
@media (max-width: 680px) {
  .invoice-print { padding: 20px 16px; }
}
</style>