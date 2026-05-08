"use strict";

document.addEventListener('DOMContentLoaded', function () {[[]]
    document.querySelectorAll('.js-tagify').forEach(function (el) {
        var initialValue = el.value;
        var tagify = new Tagify(el, {
            originalInputValueFormat: function (valuesArr) {
                return JSON.stringify(valuesArr.map(function (item) {
                    return { value: item.value };
                }));
            }
        });

        if (initialValue) {
            tagify.removeAllTags();
            try {
                tagify.addTags(JSON.parse(initialValue));
            } catch (e) {
                var tags = initialValue.split(',')
                    .filter(function (t) { return t.trim(); })
                    .map(function (t) { return { value: t.trim() }; });
                tagify.addTags(tags);
            }
        }
    });

    // ===== CKEDITOR =====
    if (window.KTCKEditor4) {
        KTCKEditor4.init();
    }

    var btns = document.querySelectorAll('.btn-locale');
    if (btns.length === 0) return;

    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var locale = this.dataset.locale;

            btns.forEach(function (b) {
                b.classList.remove('btn-primary');
                b.classList.add('btn-light');
            });
            this.classList.add('btn-primary');
            this.classList.remove('btn-light');

            document.querySelectorAll('.locale-fields').forEach(function (el) {
                el.style.display = el.dataset.locale === locale ? '' : 'none';
            });

            if (window.KTCKEditor4) {
                KTCKEditor4.initAll();
            }
        });
    });

});
"use strict";

document.addEventListener('DOMContentLoaded', function () {

    const submitBtn = document.getElementById('bnt-submit');

    if (submitBtn) {

        submitBtn.addEventListener('click', async function () {

            const form = document.querySelector('form');

            const route = this.dataset.route;

            const formData = new FormData(form);

            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            try {

                const response = await fetch(route, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Submit failed');
                }

                await Swal.fire({
                    text: result.message || 'Success',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });

                if (result.redirect) {
                    window.location.href = result.redirect;
                }

            } catch (e) {

                Swal.fire({
                    text: e.message || 'Something went wrong',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });

            } finally {

                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;

            }

        });

    }

});