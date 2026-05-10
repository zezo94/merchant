<form method="GET" action="{{ route('merchants.index') }}" class="merchants-tools-form">
    <div class="accordion mb-4" id="merchantToolsAccordion">
        <div class="accordion-item merchants-accordion-item">
            <h2 class="accordion-header" id="filtersHeading">
                <button
                    class="accordion-button merchants-accordion-button"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#filtersCollapse"
                    aria-expanded="true"
                    aria-controls="filtersCollapse"
                >
                    الفلاتر والبحث
                </button>
            </h2>

            <div
                id="filtersCollapse"
                class="accordion-collapse collapse hiding"
                aria-labelledby="filtersHeading"
                data-bs-parent="#merchantToolsAccordion"
            >
                <div class="accordion-body">
                    <div class="row g-3">

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">عدد السجلات بالصفحة</label>
                            <select name="per_page" class="form-select">
                                @foreach([10, 25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (string) request('per_page', 20) === (string) $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">بحث عام</label>
                            <input
                                type="text"
                                name="global"
                                value="{{ request('global') }}"
                                class="form-control"
                                placeholder="اسم، هاتف، إيميل، سجل، قطاع..."
                            >
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">رقم العضوية</label>
                            <input type="text" name="membership_no" value="{{ request('membership_no') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">اسم الشركة</label>
                            <input type="text" name="organization_name" value="{{ request('organization_name') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">الاسم التجاري</label>
                            <input type="text" name="commercial_name" value="{{ request('commercial_name') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">رقم السجل التجاري</label>
                            <input type="text" name="commercial_reg_no" value="{{ request('commercial_reg_no') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">الرقم الوطني</label>
                            <input type="text" name="org_national_no" value="{{ request('org_national_no') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">القطاع</label>
                            <input type="text" name="sector" value="{{ request('sector') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">العنوان</label>
                            <input type="text" name="street" value="{{ request('street') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">الوصف</label>
                            <input type="text" name="description" value="{{ request('description') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">المفوّض بالتوقيع</label>
                            <input type="text" name="delegate" value="{{ request('delegate') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">الأعضاء / الشركاء</label>
                            <input type="text" name="members" value="{{ request('members') }}" class="form-control">
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">ملاحظات</label>
                            <input type="text" name="notes" value="{{ request('notes') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الهاتف</label>
                            <input type="text" name="phone" value="{{ request('phone') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الموبايل</label>
                            <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الإيميل</label>
                            <input type="text" name="email" value="{{ request('email') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الفاكس</label>
                            <input type="text" name="fax" value="{{ request('fax') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">صندوق البريد</label>
                            <input type="text" name="po_box" value="{{ request('po_box') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">وصف الرمز البريدي</label>
                            <input type="text" name="zipcode_desc" value="{{ request('zipcode_desc') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الرمز البريدي</label>
                            <input type="text" name="zipcode" value="{{ request('zipcode') }}" class="form-control">
                        </div>



                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">تاريخ التسجيل</label>
                            <input type="date" name="registered_date" value="{{ request('registered_date') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">تاريخ السجل التجاري</label>
                            <input type="date" name="commercial_reg_date" value="{{ request('commercial_reg_date') }}" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">تم التواصل</label>
                            <select name="contacted" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('contacted') === '1' ? 'selected' : '' }}>نعم</option>
                                <option value="0" {{ request('contacted') === '0' ? 'selected' : '' }}>لا</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="filter-label">الدعوة</label>
                            <select name="invited" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('invited') === '1' ? 'selected' : '' }}>نعم</option>
                                <option value="0" {{ request('invited') === '0' ? 'selected' : '' }}>لا</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2 flex-wrap merchants-tools-actions">
                        <button type="submit" class="btn btn-success">تطبيق الفلاتر</button>
                        <a href="{{ route('merchants.index') }}" class="btn btn-secondary">إعادة ضبط</a>
                    </div>
                </div>
            </div>


        </div>

        <div class="accordion-item merchants-accordion-item">
            <h2 class="accordion-header" id="columnsHeading">
                <button
                    class="accordion-button collapsed merchants-accordion-button"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#columnsCollapse"
                    aria-expanded="false"
                    aria-controls="columnsCollapse"
                >
                    الأعمدة المعروضة
                </button>
            </h2>

            <div
                id="columnsCollapse"
                class="accordion-collapse collapse"
                aria-labelledby="columnsHeading"
                data-bs-parent="#merchantToolsAccordion"
            >
                <div class="accordion-body">
                    <div class="row g-2">
                        @foreach($columnLabels as $key => $label)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <label class="form-check merchants-column-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="columns[]"
                                        value="{{ $key }}"
                                        {{ in_array($key, $selectedColumns, true) ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label">{{ $label }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">حفظ الأعمدة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item merchants-accordion-item">
            <h2 class="accordion-header" id="printColumnsHeading">
                <button
                    class="accordion-button collapsed merchants-accordion-button"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#printColumnsCollapse"
                    aria-expanded="false"
                    aria-controls="printColumnsCollapse"
                >
                    أعمدة الطباعة والتصدير
                </button>
            </h2>

            <div
                id="printColumnsCollapse"
                class="accordion-collapse collapse"
                aria-labelledby="printColumnsHeading"
                data-bs-parent="#merchantToolsAccordion"
            >
                <div class="accordion-body">
                    <div class="row g-2">
                        @foreach($columnLabels as $key => $label)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <label class="form-check merchants-column-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="print_columns[]"
                                        value="{{ $key }}"
                                        {{ in_array($key, $selectedPrintColumns, true) ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label">{{ $label }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">حفظ أعمدة الطباعة</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>


