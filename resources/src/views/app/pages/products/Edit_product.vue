<template>
  <div class="main-content">
    <breadcumb :page="'Update Product'" :folder="$t('Products')"/>
    <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>

    <validation-observer ref="Edit_Product" v-if="!isLoading">
      <b-form @submit.prevent="Submit_Product" enctype="multipart/form-data">
         <b-row>
          <b-col md="8" class="mb-2">
            <b-card class="mt-3">
              <b-row>
                <!-- Name -->
                <b-col md="6" class="mb-2">
                  <validation-provider
                    name="Name"
                    :rules="{required:true , min:3 , max:55}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Name_product') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="Name-feedback"
                        label="Name"
                        :placeholder="$t('Enter_Name_Product')"
                        v-model="product.name"
                      ></b-form-input>
                      <b-form-invalid-feedback id="Name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Barcode Symbology  -->
                <b-col md="6" class="mb-2">
                  <validation-provider name="Barcode Symbology" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('BarcodeSymbology') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        v-model="product.Type_barcode"
                        :reduce="label => label.value"
                        :placeholder="$t('Choose_Symbology')"
                        :options="
                            [
                              {label: 'Code 128', value: 'CODE128'},
                              {label: 'Code 39', value: 'CODE39'},
                              {label: 'EAN8', value: 'EAN8'},
                              {label: 'EAN13', value: 'EAN13'},
                              {label: 'UPC', value: 'UPC'},
                            ]"
                      ></v-select>
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Code Product"-->
                <b-col md="6" class="mb-2">
                  <validation-provider name="Code Product" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('CodeProduct') + ' ' + '*'"
                    >
                      <div class="input-group">
                        <b-form-input
                          :class="{'is-invalid': !!errors.length}"
                          :state="errors[0] ? false : (valid ? true : null)"
                          aria-describedby="CodeProduct-feedback"
                          type="text"
                          v-model="product.code"
                        ></b-form-input>
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <a @click="generateNumber()">
                              <i class="i-Bar-Code cursor-pointer"></i>
                            </a>
                          </span>
                        </div>
                        <b-form-invalid-feedback id="CodeProduct-feedback">{{ errors[0] }}</b-form-invalid-feedback>
                      </div>
                      <span>{{$t('Scan_your_barcode_and_select_the_correct_symbology_below')}}</span>
                      <b-alert
                        show
                        variant="danger"
                        class="error mt-1"
                        v-if="code_exist !=''"
                      >{{code_exist}}</b-alert>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Category -->
                <b-col md="6" class="mb-2">
                  <validation-provider name="category" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('Categorie') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        :reduce="label => label.value"
                        :placeholder="$t('Choose_Category')"
                        v-model="product.category_id"
                        :options="categories.map(categories => ({label: categories.name, value: categories.id}))"
                      />
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Brand  -->
                <b-col md="6" class="mb-2">
                  <b-form-group :label="$t('Brand')">
                    <v-select
                      :placeholder="$t('Choose_Brand')"
                      :reduce="label => label.value"
                      v-model="product.brand_id"
                      :options="brands.map(brands => ({label: brands.name, value: brands.id}))"
                    />
                  </b-form-group>
                </b-col>

                <!-- Order Tax -->
                <b-col md="6" class="mb-2">
                  <validation-provider
                    name="Order Tax"
                    :rules="{regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('OrderTax')">
                      <div class="input-group">
                        <input
                          :state="getValidationState(validationContext)"
                          aria-describedby="OrderTax-feedback"
                          v-model.number="product.TaxNet"
                          type="text"
                          class="form-control"
                        >
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                      <b-form-invalid-feedback
                        id="OrderTax-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Tax Method -->
                <b-col lg="6" md="6" sm="12" class="mb-2">
                  <validation-provider name="Tax Method" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('TaxMethod') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        v-model="product.tax_method"
                        :reduce="label => label.value"
                        :placeholder="$t('Choose_Method')"
                        :options="
                           [
                            {label: 'Exclusive', value: '1'},
                            {label: 'Inclusive', value: '2'}
                           ]"
                      ></v-select>
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <b-col md="12" class="mb-2">
                  <b-form-group :label="$t('Description')">
                    <textarea
                      rows="4"
                      class="form-control"
                      :placeholder="$t('Afewwords')"
                      v-model="product.note"
                    ></textarea>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-card>

            <b-card class="mt-3">
              <b-row>
              
                 <!-- type -->

                <b-col md="6" class="mb-2" v-if="product.type == 'is_single'">
                  <b-form-group :label="$t('type')">
                    <b-form-input
                      label="Product Type"
                      value="Standard Product"
                      disabled="disabled"
                    ></b-form-input>
                  </b-form-group>
                </b-col>

               <b-col md="6" class="mb-2" v-else-if="product.type == 'is_service'">
                  <b-form-group :label="$t('type')">
                    <b-form-input
                      label="Product Type"
                      value="Service Product"
                      disabled="disabled"
                    ></b-form-input>
                  </b-form-group>
                </b-col>

                <b-col md="6" class="mb-2" v-else-if="product.type == 'is_variant'">
                  <b-form-group :label="$t('type')">
                    <b-form-input
                      label="Product Type"
                      value="Variable Product"
                      disabled="disabled"
                    ></b-form-input>
                  </b-form-group>
                </b-col>

                <!-- Product Cost -->
                <b-col md="6" class="mb-2" v-if="product.type == 'is_single'">
                  <validation-provider
                    name="Product Cost"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('ProductCost') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductCost-feedback"
                        label="Cost"
                        :placeholder="$t('Enter_Product_Cost')"
                        v-model="product.cost"
                      ></b-form-input>
                      <b-form-invalid-feedback
                        id="ProductCost-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
                    <!-- Cost Percentage -->
                <b-col md="6" class="mb-2" v-if="product.type == 'is_single'">
                  <validation-provider
                    name=" Cost Percentage"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Cost Percentage') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductCost-feedback"
                        label="Cost"
                        :placeholder="$t('Enter Product Cost Percentage')"
                        v-model="product.cost_percentage"
                      ></b-form-input>
                      <b-form-invalid-feedback
                        id="ProductCost-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
 <!-- Label Price -->
<b-col md="6" class="mb-2" v-if="product.type == 'is_single'">
  <validation-provider
    name="Label Price"
    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
    v-slot="validationContext"
  >
    <b-form-group :label="$t('Label Price') + ' ' + '*'">
      <b-form-input
        :state="getValidationState(validationContext)"
        aria-describedby="LabelPrice-feedback"
        label="Label Price"
        :placeholder="$t('Enter Label Price')"
        v-model="product.label_price"
      ></b-form-input>
      <b-form-invalid-feedback id="LabelPrice-feedback">
        {{ validationContext.errors[0] }}
      </b-form-invalid-feedback>
    </b-form-group>
  </validation-provider>
</b-col>
                <!-- Product Price
                <b-col
                  md="6"
                  class="mb-2"
                  v-if="product.type == 'is_single' || product.type == 'is_service'"
                >
                  <validation-provider
                    name="Product Price"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('ProductPrice') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductPrice-feedback"
                        label="Price"
                        :placeholder="$t('Enter_Product_Price')"
                        v-model="product.price"
                      ></b-form-input>

                      <b-form-invalid-feedback
                        id="ProductPrice-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col> -->
  <!-- Product Retail Price Percentage-->
  <b-col
                  md="6"
                  class="mb-2"
                  v-if="product.type == 'is_single' "
                >
                  <validation-provider
                    name=" Product Retail Price Percentage"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Product Retail Price Percentage') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductPrice-feedback"
                        label="Price"
                        :placeholder="$t('Enter_Product_Price')"
                        v-model=" product.retail_price_percentage"
                      ></b-form-input>

                      <b-form-invalid-feedback
                        id="ProductPrice-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
                    <!-- Retail Product  Price -->
                    <b-col
                  md="6"
                  class="mb-2"
                  v-if="product.type == 'is_single' || product.type == 'is_service'"
                >
                  <validation-provider
                    name="Product Price"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Retail Product Price') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductPrice-feedback"
                        label="Price"
                        :placeholder="$t('Enter_Product_Price')"
                        v-model="product.price"
                      ></b-form-input>

                      <b-form-invalid-feedback
                        id="ProductPrice-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
                
                    <!-- Product Wholesale Price Percentage -->
                    <b-col
                  md="6"
                  class="mb-2"
                  v-if="product.type == 'is_single' "
                >
                  <validation-provider
                    name="Product Wholesale Price Percentag"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Product Wholesale Price Percentage') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductPrice-feedback"
                        label="Price"
                        :placeholder="$t('Enter_Product_Price')"
                        v-model="product.wholesale_price_percentage"
                      ></b-form-input>

                      <b-form-invalid-feedback
                        id="ProductPrice-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                    <!-- Product Wholesale Price  -->
                    <b-col
                  md="6"
                  class="mb-2"
                  v-if="product.type == 'is_single' || product.type == 'is_service'"
                >
                  <validation-provider
                    name="Product Wholesale Price"
                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('Product Wholesale Price') + ' ' + '*'">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="ProductPrice-feedback"
                        label="Price"
                        :placeholder="$t('Enter_Product_Price')"
                        v-model="product.wholesale_price"
                      ></b-form-input>

                      <b-form-invalid-feedback
                        id="ProductPrice-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
                <!-- Unit Product -->
                <b-col md="6" class="mb-2" v-if="product.type != 'is_service'">
                  <validation-provider name="Unit Product" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('UnitProduct') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        v-model="product.unit_id"
                        class="required"
                        required
                        @input="Selected_Unit"
                        :placeholder="$t('Choose_Unit_Product')"
                        :reduce="label => label.value"
                        :options="units.map(units => ({label: units.name, value: units.id}))"
                      />
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Unit Sale -->
                <b-col md="6" class="mb-2" v-if="product.type != 'is_service'">
                  <validation-provider name="Unit Sale" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('UnitSale') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        v-model="product.unit_sale_id"
                        :placeholder="$t('Choose_Unit_Sale')"
                        :reduce="label => label.value"
                        :options="units_sub.map(units_sub => ({label: units_sub.name, value: units_sub.id}))"
                      />
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Unit Purchase -->
                <b-col md="6" class="mb-2" v-if="product.type != 'is_service'">
                  <validation-provider name="Unit Purchase" :rules="{ required: true}">
                    <b-form-group
                      slot-scope="{ valid, errors }"
                      :label="$t('UnitPurchase') + ' ' + '*'"
                    >
                      <v-select
                        :class="{'is-invalid': !!errors.length}"
                        :state="errors[0] ? false : (valid ? true : null)"
                        v-model="product.unit_purchase_id"
                        :placeholder="$t('Choose_Unit_Purchase')"
                        :reduce="label => label.value"
                        :options="units_sub.map(units_sub => ({label: units_sub.name, value: units_sub.id}))"
                      />
                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Stock Alert -->
                <b-col md="6" class="mb-2" v-if="product.type != 'is_service'">
                  <validation-provider
                    name="Stock Alert"
                    :rules="{ regex: /^\d*\.?\d*$/}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('StockAlert')">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="StockAlert-feedback"
                        label="Stock alert"
                        :placeholder="$t('Enter_Stock_alert')"
                        v-model="product.stock_alert"
                      ></b-form-input>
                      <b-form-invalid-feedback
                        id="StockAlert-feedback"
                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <div class="col-md-9 mb-3 mt-3" v-if="product.type == 'is_variant'">
                  <div class="d-flex">
                    <input
                      style="height: 40px;"
                      placeholder="Enter the Variant"
                      type="text"
                      name="variant"
                      v-model="tag"
                      class="form-control"
                    >
                    <a
                      style="color: #ffff;margin-left: 10px;"
                      @click="add_variant(tag)"
                      class="ms-3 btn btn-md btn-primary"
                    >{{$t('Add')}}</a>
                  </div>
                </div>

                <div class="col-md-12 mb-2" v-if="product.type == 'is_variant'">
                  <div class="table-responsive">
                    <table class="table table-hover table-sm">
                      <thead class="bg-gray-300">
                        <tr>
                          <th scope="col">{{$t('Variant_code')}}</th>
                          <th scope="col">{{$t('Variant_Name')}}</th>
                          <th scope="col">{{$t('Variant_cost')}}</th>
                          <th scope="col">{{$t('Cost Percentage')}}</th>
                          <th scope="col">{{$t('Label Price')}}</th>
                          <th>{{$t('Variant_Retail_percentage')}}</th>
                           <th>{{$t('Retail_price')}}</th>
                           <th>{{$t('Variant_wholesale_percentage')}}</th>
                           <th>{{$t('Variant_wholesale_price')}}</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="variants.length <=0">
                          <td colspan="3">{{$t('NodataAvailable')}}</td>
                        </tr>
                        <!-- <tr v-for="variant in variants">
                          <td>
                            <input required class="form-control" v-model="variant.code">
                          </td>
                          <td>
                            <input  required class="form-control" v-model="variant.text">
                          </td>
                          <td>
                            <input required class="form-control" v-model="variant.cost">
                          </td>
                          <td>
                            <input required class="form-control" v-model="variant.price">
                          </td>
                          <td>
                            <a
                              style="color: #ffff;"
                              @click="delete_variant(variant.var_id)"
                              class="btn btn-sm btn-danger"
                              title="Delete"
                            >
                              <i class="i-Close-Window"></i>
                            </a>
                          </td>
                        </tr> -->
                    
                        <tr v-for="(variant) in variants" :key="variant.var_id">
      <td>
        <input required class="form-control" v-model="variant.code">
      </td>
      <td>
        <input required class="form-control" v-model="variant.text">
      </td>
      <td>
        <input required class="form-control" 
               v-model.number="variant.cost"
               @change="calculateVariantPrices(variant)">
      </td>
            <td>
        <input required class="form-control" 
               v-model.number="variant.cost_percentage"
              >
      </td>
       <td>
        <input required class="form-control" 
               v-model.number="variant.label_price"
               @change="calculateVariantPrices(variant)">
      </td>
      <td>
        <input required class="form-control" 
               v-model.number="variant.retail_price_percentage"
               @change="calculateVariantPrices(variant)">
      </td>
      <td>
        <input required class="form-control" v-model="variant.price" >
      </td>
      <td>
        <input required class="form-control" 
               v-model.number="variant.wholesale_price_percentage"
               @change="calculateVariantPrices(variant)">
      </td>
      <td>
        <input required class="form-control" v-model="variant.wholesale_price" >
      </td>
      <td>
        <a @click="delete_variant(variant.var_id)" class="btn btn-sm btn-danger">
          <i class="i-Close-Window"></i>
        </a>
      </td>
    </tr>
                    
                    
                      </tbody>
                    </table>
                  </div>
                </div>
              </b-row>
            </b-card>
           
            <b-card class="mt-3">
              <b-row>
                <!-- Product_Has_Imei_Serial_number -->
                <b-col md="12 mb-2">
                  <ValidationProvider rules vid="product" v-slot="x">
                    <div class="form-check">
                      <label class="checkbox checkbox-outline-primary">
                        <input type="checkbox" v-model="product.is_imei">
                        <h5>{{$t('Product_Has_Imei_Serial_number')}}</h5>
                        <span class="checkmark"></span>
                      </label>
                    </div>
                  </ValidationProvider>
                </b-col>

                <!-- This_Product_Not_For_Selling -->
                <b-col md="12 mb-2">
                  <ValidationProvider rules vid="product" v-slot="x">
                    <div class="form-check">
                      <label class="checkbox checkbox-outline-primary">
                        <input type="checkbox" v-model="product.not_selling">
                        <h5>{{$t('This_Product_Not_For_Selling')}}</h5>
                        <span class="checkmark"></span>
                      </label>
                    </div>
                  </ValidationProvider>
                </b-col>
              </b-row>
            </b-card>
          </b-col>

          <b-col md="4">
            <!-- upload-multiple-image -->
            <b-card>
              <div class="card-header">
                <h5>{{$t('MultipleImage')}}</h5>
              </div>
              <div class="card-body">
                <b-row class="form-group">
                  <b-col md="12 mb-5">
                    <div
                      id="my-strictly-unique-vue-upload-multiple-image"
                      class="d-flex justify-content-center"
                    >
                      <vue-upload-multiple-image
                      @upload-success="uploadImageSuccess"
                      @before-remove="beforeRemove"
                      dragText="Drag & Drop Multiple images For product"
                      dropText="Drag & Drop image"
                      browseText="(or) Select"
                      accept=image/gif,image/jpeg,image/png,image/bmp,image/jpg
                      primaryText='success'
                      markIsPrimaryText='success'
                      popupText='have been successfully uploaded'
                      :data-images="images"
                      idUpload="myIdUpload"
                      :showEdit="false"
                      />
                    </div>
                  </b-col>
                </b-row>
              </div>
            </b-card>
          </b-col>
          <b-col md="12" class="mt-3">
            <b-button variant="primary" type="submit" :disabled="SubmitProcessing"><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
            <div v-once class="typo__p" v-if="SubmitProcessing">
              <div class="spinner sm spinner-primary mt-3"></div>
            </div>
          </b-col>
        </b-row>
      </b-form>
    </validation-observer>
  </div>
</template>

<script>
import VueUploadMultipleImage from "vue-upload-multiple-image";
import VueTagsInput from "@johmun/vue-tags-input";
import NProgress from "nprogress";
export default {
  metaInfo: {
    title: "Edit Product"
  },
  data() {
    return {
      tag: "",
      len: 8,
      images: [],
      imageArray: [],
      change: false,
      isLoading: true,
      SubmitProcessing:false,
      data: new FormData(),
      categories: [],
      Subcategories: [],
      units: [],
      units_sub: [],
      brands: [],
      roles: {},
      variants: [],
      // Add this flag to control when calculations should happen
      shouldCalculate: false,
      product: {
        type: "",
        name: "",
        code: "",
        Type_barcode: "",
        cost: "",
        price: "",
        brand_id: "",
        category_id: "",
        TaxNet: "",
        tax_method: "1",
        unit_id: "",
        unit_sale_id: "",
        unit_purchase_id: "",
        stock_alert: "",
        image: "",
        note: "",
        is_variant: false,
        is_imei: false,
        not_selling: false,
        retail_price_percentage: "",
        wholesale_price_percentage: "",
        wholesale_price: "",
        label_price:"",
        cost_percentage: "",
      },
      code_exist: ""
    };
  },

  components: {
    VueUploadMultipleImage,
    VueTagsInput
  },
  
  // Update watchers to check the shouldCalculate flag
  watch: {
    "product.retail_price_percentage": function(newValue) {
      if (!this.shouldCalculate) return;
      this.calculatePrices();
    },
    "product.wholesale_price_percentage": function(newValue) {
      if (!this.shouldCalculate) return;
      this.calculatePrices();
    },
    "product.label_price": function(newValue) {
      if (!this.shouldCalculate) return;
      this.calculatePrices();
      
      // Update all variants' cost if they're empty
      this.variants.forEach(variant => {
        if (!variant.label_price || variant.label_price === 0) {
          variant.label_price = newValue;
        }
        this.calculateVariantPrices(variant);
      });
    },
  },
 
  methods: {
    calculateVariantPrices(variant) {
      if (!this.shouldCalculate) return;
      
      const cost = parseFloat(variant.label_price) || 0;
      
      // Calculate retail price
      if (variant.retail_price_percentage !== undefined) {
        const retailPercentage = parseFloat(variant.retail_price_percentage) || 0;
        variant.price = (cost - (cost * retailPercentage / 100)).toFixed(2);
      }
      
      // Calculate wholesale price
      if (variant.wholesale_price_percentage !== undefined) {
        const wholesalePercentage = parseFloat(variant.wholesale_price_percentage) || 0;
        variant.wholesale_price = (cost - (cost * wholesalePercentage / 100)).toFixed(2);
      }
    },

    calculateAllVariantPrices() {
      if (!this.shouldCalculate) return;
      
      this.variants.forEach(variant => {
        this.calculateVariantPrices(variant);
      });
    },

    calculatePrices() {
      if (!this.shouldCalculate) return;
      
      try {
        const cost = parseFloat(this.product.label_price) || 0;
        
        // Retail price calculation
        if (this.product.retail_price_percentage !== "" && !isNaN(this.product.retail_price_percentage)) {
          const retailPercentage = parseFloat(this.product.retail_price_percentage);
          if (retailPercentage >= 0) {
            this.product.price = (cost - (cost * retailPercentage / 100)).toFixed(2);
          }
        }

        // Wholesale price calculation
        if (this.product.wholesale_price_percentage !== "" && !isNaN(this.product.wholesale_price_percentage)) {
          const wholesalePercentage = parseFloat(this.product.wholesale_price_percentage);
          if (wholesalePercentage >= 0) {
            this.product.wholesale_price = (cost - (cost * wholesalePercentage / 100)).toFixed(2);
          }
        }
      } catch (error) {
        console.error("Error in price calculation:", error);
      }
    },

    //------ Generate code
    generateNumber() {
      this.code_exist = "";
      this.product.code = Math.floor(
        Math.pow(10, 7) +
          Math.random() *
            (Math.pow(10, 8) - Math.pow(10, 7) - 1)
      );
    },
    
    //------------- Submit Validation Update Product
    Submit_Product() {
      this.$refs.Edit_Product.validate().then(success => {
        if (!success) {
          this.makeToast(
            "danger",
            this.$t("Please_fill_the_form_correctly"),
            this.$t("Failed")
          );
        } else {
            if (this.product.type == 'is_variant' && this.variants.length <= 0) {
              this.makeToast("danger", "The variants array is required.", this.$t("Failed"));
            } else {
              this.Update_Product();
            }
        }
      });
    },

    //------ Validation state fields
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

    //------ Toast
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },

    add_variant(tag) {
      if (
        this.variants.length > 0 &&
        this.variants.some(variant => variant.text === tag)
      ) {
        this.makeToast("warning", this.$t("VariantDuplicate"), this.$t("Warning"));
      } else {
        if (this.tag !== '') {
          // Enable calculations for new variants
          const shouldCalculateState = this.shouldCalculate;
          this.shouldCalculate = true;
          
          const cost = parseFloat(this.product.label_price) || 0;
          const retailPercentage = parseFloat(this.product.retail_price_percentage) || 0;
          const wholesalePercentage = parseFloat(this.product.wholesale_price_percentage) || 0;

          const variant = {
            var_id: this.variants.length + 1,
            text: tag,
            code: '',
            cost,
            label_price: cost,
              cost_percentage: this.product.cost_percentage || 0,
            retail_price_percentage: retailPercentage,
            wholesale_price_percentage: wholesalePercentage,
            price: (cost - (cost * retailPercentage / 100)).toFixed(2),
            wholesale_price: (cost - (cost * wholesalePercentage / 100)).toFixed(2),
          };

          this.variants.push(variant);
          this.tag = "";
          
          // Restore the calculation state
          this.shouldCalculate = shouldCalculateState;
        } else {
          this.makeToast("warning", "Please Enter the Variant", this.$t("Warning"));
        }
      }
    },
    
    delete_variant(var_id) {
      for (var i = 0; i < this.variants.length; i++) {
        if (var_id === this.variants[i].var_id) {
          this.variants.splice(i, 1);
        }
      }
    },

    //------ event upload Image Success
    uploadImageSuccess(formData, index, fileList, imageArray) {
      this.images = fileList;
    },

    //------ event before Remove image
    beforeRemove(index, done, fileList) {
      var remove = confirm("remove image");
      if (remove == true) {
        this.images.splice(index, 1);
        done();
      } else {
      }
    },

    //---------------------------------------Get Product Elements ------------------------------\\
    GetElements() {
      let id = this.$route.params.id;
      axios
        .get(`products/${id}/edit`)
        .then(response => {
          // Disable calculations while loading API data
          this.shouldCalculate = false;
          
          this.product = response.data.product;
          this.variants = response.data.product.ProductVariant;
          this.images = response.data.product.images;
          this.categories = response.data.categories;
          this.brands = response.data.brands;
          this.units = response.data.units;
          this.units_sub = response.data.units_sub;
          this.Subcategories = response.data.Subcategories;

          this.isLoading = false;
          
          // Enable calculations after a short delay to ensure data is fully loaded
          setTimeout(() => {
            this.shouldCalculate = true;
          }, 100);
        })
        .catch(response => {
          setTimeout(() => {
            this.isLoading = false;
            this.shouldCalculate = true;
          }, 500);
        });
    },

    //---------------------- Get Sub Units with Unit id ------------------------------\\
    Get_Units_SubBase(value) {
      axios
        .get("get_sub_units_by_base?id=" + value)
        .then(({ data }) => (this.units_sub = data));
    },

    //---------------------- Event Select Unit Product ------------------------------\\
    Selected_Unit(value) {
      this.units_sub = [];
      this.product.unit_sale_id = "";
      this.product.unit_purchase_id = "";
      this.Get_Units_SubBase(value);
    },

    //------------------------------ Update Product ------------------------------\\
    Update_Product() {
      NProgress.start();
      NProgress.set(0.1);
      var self = this;
      self.data = new FormData();
      self.SubmitProcessing = true;

      if (self.product.type == 'is_variant' && self.variants.length > 0) {
        self.product.is_variant = true;
      } else {
        self.product.is_variant = false;
      }

      // append object product
      Object.entries(self.product).forEach(([key, value]) => {
          self.data.append(key, value);
      });
                
      // append array variants
      if (self.variants.length) {
          for (var i = 0; i < self.variants.length; i++) {
          Object.entries(self.variants[i]).forEach(([key, value]) => {
              self.data.append("variants[" + i + "][" + key + "]", value);
          });
          }
      }

      // append array images
      if (self.images.length > 0) {
        for (var k = 0; k < self.images.length; k++) {
          Object.entries(self.images[k]).forEach(([key, value]) => {
            self.data.append("images[" + k + "][" + key + "]", value);
          });
        }
      }

      self.data.append("_method", "put");

      // send Data with axios
      axios
        .post("products/" + this.product.id, self.data)
        .then(response => {
          NProgress.done();
          self.SubmitProcessing = false;
          this.$router.push({ name: "index_products" });
          this.makeToast(
            "success",
            this.$t("Successfully_Updated"),
            this.$t("Success")
          );
        })
        .catch(error => {
            NProgress.done();
            self.SubmitProcessing = false;
            if (error.errors.code && error.errors.code.length > 0) {
              self.code_exist = error.errors.code[0];
              this.makeToast("danger", error.errors.code[0], this.$t("Failed"));
            } else if(error.errors.variants && error.errors.variants.length > 0) {
              this.makeToast("danger", error.errors.variants[0], this.$t("Failed"));
            } else {
              this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
            }
        });
    }
  }, // end Methods

  //-----------------------------Created function-------------------
  created: function() {
    // Disable calculations initially
    this.shouldCalculate = false;
    this.GetElements();
    this.imageArray = [];
    this.images = [];
  }
};
// export default {
//   metaInfo: {
//     title: "Edit Product"
//   },
//   data() {
//     return {
//       tag: "",
//       len: 8,
//       images: [],
//       imageArray: [],
//       change: false,
//       isLoading: true,
//       SubmitProcessing:false,
//       data: new FormData(),
//       categories: [],
//       Subcategories: [],
//       units: [],
//       units_sub: [],
//       brands: [],
//       roles: {},
//       variants: [],
//       product: {
//         type: "",
//         name: "",
//         code: "",
//         Type_barcode: "",
//         cost: "",
//         price: "",
//         brand_id: "",
//         category_id: "",
//         TaxNet: "",
//         tax_method: "1",
//         unit_id: "",
//         unit_sale_id: "",
//         unit_purchase_id: "",
//         stock_alert: "",
//         image: "",
//         note: "",
//         is_variant: false,
//         is_imei: false,
//         not_selling: false,
//         retail_price_percentage: "",
//         wholesale_price_percentage: "",
//         wholesale_price: "",
//         label_price:"",

//       },
//       code_exist: ""
//     };
//   },

//   components: {
//     VueUploadMultipleImage,
//     VueTagsInput
//   },
//   watch: {
//     "product.retail_price_percentage": function(newValue) {
//       // Recalculate retail price only when retail percentage changes
//       this.calculatePrices();
//     },
//     "product.wholesale_price_percentage": function(newValue) {
//       // Recalculate wholesale price only when wholesale percentage changes
//       this.calculatePrices();
//     },
//     "product.label_price": function(newValue) {
//       // Recalculate both prices when cost changes
//       this.calculatePrices();
//     },
//   "product.label_price": function(newValue) {
//     this.calculatePrices();
//     // Update all variants' cost if they're empty
//     this.variants.forEach(variant => {
//       if (!variant.label_price || variant.label_price === 0) {
//         variant.label_price = newValue;
//       }
//       this.calculateVariantPrices(variant);
//     });
//   },
//   "product.retail_price_percentage": function(newValue) {
//     this.calculatePrices();
//     // Update all variants' percentage if they're empty
//     this.variants.forEach(variant => {
//       if (!variant.retail_price_percentage || variant.retail_price_percentage === 0) {
//         variant.retail_price_percentage = newValue;
//         this.calculateVariantPrices(variant);
//       }
//     });
//   },
//   "product.wholesale_price_percentage": function(newValue) {
//     this.calculatePrices();
//     // Update all variants' percentage if they're empty
//     this.variants.forEach(variant => {
//       if (!variant.wholesale_price_percentage || variant.wholesale_price_percentage === 0) {
//         variant.wholesale_price_percentage = newValue;
//         this.calculateVariantPrices(variant);
//       }
//     });
//   },
// },
 
 
//   methods: {
//     calculateVariantPrices(variant) {
//     const cost = parseFloat(variant.label_price) || 0;
    
//     // Calculate retail price
//     if (variant.retail_price_percentage !== undefined) {
//       const retailPercentage = parseFloat(variant.retail_price_percentage) || 0;
//       variant.price = (cost - (cost * retailPercentage / 100)).toFixed(2);
//     }
    
//     // Calculate wholesale price
//     if (variant.wholesale_price_percentage !== undefined) {
//       const wholesalePercentage = parseFloat(variant.wholesale_price_percentage) || 0;
//       variant.wholesale_price = (cost - (cost * wholesalePercentage / 100)).toFixed(2);
//     }
//   },

//   calculateAllVariantPrices() {
//     this.variants.forEach(variant => {
//       this.calculateVariantPrices(variant);
//     });
//   },
//   calculatePrices() {
//   try {
//     const cost = parseFloat(this.product.label_price) || 0;
    
//     // Retail price calculation
//     if (this.product.retail_price_percentage !== "" && !isNaN(this.product.retail_price_percentage)) {
//       const retailPercentage = parseFloat(this.product.retail_price_percentage);
//       if (retailPercentage >= 0) { // Ensure percentage is positive
//         this.product.price = (cost - (cost * retailPercentage / 100)).toFixed(2);
//       }
//     }

//     // Wholesale price calculation
//     if (this.product.wholesale_price_percentage !== "" && !isNaN(this.product.wholesale_price_percentage)) {
//       const wholesalePercentage = parseFloat(this.product.wholesale_price_percentage);
//       if (wholesalePercentage >= 0) { // Ensure percentage is positive
//         this.product.wholesale_price = (cost - (cost * wholesalePercentage / 100)).toFixed(2);
//       }
//     }
//   } catch (error) {
//     console.error("Error in price calculation:", error);
//   }
// },
//      //------ Generate code
//      generateNumber() {
//       this.code_exist = "";
//       this.product.code = Math.floor(
//         Math.pow(10, 7) +
//           Math.random() *
//             (Math.pow(10, 8) - Math.pow(10, 7) - 1)
//       );
//     },
    
//     //------------- Submit Validation Update Product
//     Submit_Product() {
//       this.$refs.Edit_Product.validate().then(success => {
//         if (!success) {
//           this.makeToast(
//             "danger",
//             this.$t("Please_fill_the_form_correctly"),
//             this.$t("Failed")
//           );
//         } else {

//             if (this.product.type == 'is_variant' && this.variants.length <= 0) {
//               this.makeToast("danger", "The variants array is required.", this.$t("Failed"));
//             }else{
//               this.Update_Product();
//             }
//         }
//       });
//     },

//     //------ Validation state fields
//     getValidationState({ dirty, validated, valid = null }) {
//       return dirty || validated ? valid : null;
//     },

//     //------ Toast
//     makeToast(variant, msg, title) {
//       this.$root.$bvToast.toast(msg, {
//         title: title,
//         variant: variant,
//         solid: true
//       });
//     },

//     // add_variant(tag) {
//     //   if (
//     //     this.variants.length > 0 &&
//     //     this.variants.some(variant => variant.text === tag)
//     //   ) {
//     //      this.makeToast(
//     //         "warning",
//     //         this.$t("VariantDuplicate"),
//     //         this.$t("Warning")
//     //       );
//     //   } else {
//     //       if(this.tag != ''){
//     //         var variant_tag = {
//     //           var_id: this.variants.length + 1, // generate unique ID
//     //           text: tag
//     //         };
//     //         this.variants.push(variant_tag);
//     //         this.tag = "";
//     //       }else{

//     //         this.makeToast(
//     //           "warning",
//     //           "Please Enter the Variant",
//     //           this.$t("Warning")
//     //         );
            
//     //       }
//     //   }
//     // },
   
//     add_variant(tag) {
//   if (
//     this.variants.length > 0 &&
//     this.variants.some(variant => variant.text === tag)
//   ) {
//     this.makeToast("warning", this.$t("VariantDuplicate"), this.$t("Warning"));
//   } else {
//     if (this.tag !== '') {
//       const cost = parseFloat(this.product.label_price) || 0;
//       const retailPercentage = parseFloat(this.product.retail_price_percentage) || 0;
//       const wholesalePercentage = parseFloat(this.product.wholesale_price_percentage) || 0;

//       const variant = {
//         var_id: this.variants.length + 1,
//         text: tag,
//         code: '',
//         cost,
//         label_price: cost,

//         retail_price_percentage: retailPercentage,
//         wholesale_price_percentage: wholesalePercentage,
//         price: (cost - (cost * retailPercentage / 100)).toFixed(2),
//         wholesale_price: (cost - (cost * wholesalePercentage / 100)).toFixed(2),
//       };

//       this.variants.push(variant);
//       this.tag = "";
//     } else {
//       this.makeToast("warning", "Please Enter the Variant", this.$t("Warning"));
//     }
//   }
// },
//     //-----------------------------------Delete variant------------------------------\\
   
//     delete_variant(var_id) {
//       for (var i = 0; i < this.variants.length; i++) {
//         if (var_id === this.variants[i].var_id) {
//           this.variants.splice(i, 1);
//         }
//       }
//     },

   

//     //------ event upload Image Success
//     uploadImageSuccess(formData, index, fileList, imageArray) {
//       this.images = fileList;
//     },

//     //------ event before Remove image
//     beforeRemove(index, done, fileList) {
//       var remove = confirm("remove image");
//       if (remove == true) {
//         this.images.splice(index, 1);
//         done();
//       } else {
//       }
//     },

//     //---------------------------------------Get Product Elements ------------------------------\\
//     GetElements() {
//       let id = this.$route.params.id;
//       axios
//         .get(`products/${id}/edit`)
//         .then(response => {
//           this.product = response.data.product;
//           this.variants = response.data.product.ProductVariant;
//           this.images = response.data.product.images;
//           this.categories = response.data.categories;
//           this.brands = response.data.brands;
//           this.units = response.data.units;
//           this.units_sub = response.data.units_sub;
//           this.Subcategories = response.data.Subcategories;

//           this.isLoading = false;
//         })
//         .catch(response => {
//           setTimeout(() => {
//             this.isLoading = false;
//           }, 500);
//         });
//     },

//     //---------------------- Get Sub Units with Unit id ------------------------------\\
//     Get_Units_SubBase(value) {
//       axios
//         .get("get_sub_units_by_base?id=" + value)
//         .then(({ data }) => (this.units_sub = data));
//     },

//     //---------------------- Event Select Unit Product ------------------------------\\
//     Selected_Unit(value) {
//       this.units_sub = [];
//       this.product.unit_sale_id = "";
//       this.product.unit_purchase_id = "";
//       this.Get_Units_SubBase(value);
//     },

//     //------------------------------ Update Product ------------------------------\\
//     Update_Product() {
      
//       NProgress.start();
//       NProgress.set(0.1);
//       var self = this;
//       self.data = new FormData();
//       self.SubmitProcessing = true;

//       if (self.product.type == 'is_variant' && self.variants.length > 0) {
//         self.product.is_variant = true;
//       }else{
//         self.product.is_variant = false;
//       }

//       // append objet product
//       Object.entries(self.product).forEach(([key, value]) => {
//           self.data.append(key, value);
//       });
                
//       //append array variants
//       if (self.variants.length) {
//           for (var i = 0; i < self.variants.length; i++) {
//           Object.entries(self.variants[i]).forEach(([key, value]) => {
//               self.data.append("variants[" + i + "][" + key + "]", value);
//           });
//           }
//       }

//       //append array images
//       if (self.images.length > 0) {
//         for (var k = 0; k < self.images.length; k++) {
//           Object.entries(self.images[k]).forEach(([key, value]) => {
//             self.data.append("images[" + k + "][" + key + "]", value);
//           });
//         }
//       }

//       self.data.append("_method", "put");

//       //send Data with axios
//       axios
//         .post("products/" + this.product.id, self.data)
//         .then(response => {
//           NProgress.done();
//           self.SubmitProcessing = false;
//           this.$router.push({ name: "index_products" });
//           this.makeToast(
//             "success",
//             this.$t("Successfully_Updated"),
//             this.$t("Success")
//           );
//         })
//         .catch(error => {
//             NProgress.done();
//             self.SubmitProcessing = false;
//             if (error.errors.code && error.errors.code.length > 0) {
//               self.code_exist = error.errors.code[0];
//               this.makeToast("danger", error.errors.code[0], this.$t("Failed"));
//             }else if(error.errors.variants && error.errors.variants.length > 0){
//               this.makeToast("danger", error.errors.variants[0], this.$t("Failed"));
//             }else{
//               this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
//             }
//         });
//     }
//   }, //end Methods

//   //-----------------------------Created function-------------------

//   created: function() {
//     this.GetElements();
//     this.imageArray = [];
//     this.images = [];
//   }
// };
</script>
