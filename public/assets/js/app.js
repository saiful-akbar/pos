class App {
  constructor() {
    this.baseUrl = document
      .querySelector("meta[name=base-url]")
      .getAttribute("content");

    this.csrfToken = document
      .querySelector("meta[name=csrf-token]")
      .getAttribute("content");
  }

  /**
   * Inisialisasi datatable.
   */
  dataTable(element, options) {
    return new DataTable(element, {
      processing: true,
      scrollX: true,
      fixedHeader: true,
      language: {
        search: "",
        searchPlaceholder: "Cari...",
        lengthMenu: "_MENU_",
        info: "_START_ - _END_ dari _TOTAL_",
        infoEmpty: "0 - 0 dari 0",
        infoFiltered: "",
        zeroRecords: "Tidak ditemukan data yang sesuai",
        emptyTable: "Tidak ada data yang tersedia",
      },
      lengthMenu: [
        [10, 25, 50, 100, 200],
        [10, 25, 50, 100, 200],
      ],
      layout: {
        topStart: "pageLength",
        topEnd: "search",
        bottomStart: "info",
        bottomEnd: "paging",
      },
      preDrawCallback: function () {
        $(`${element}_wrapper`).find("thead tr th").addClass("bg-light");
        $(`${element}_wrapper`).find("tfoot tr th").addClass("bg-light");
        $(`${element}_wrapper`).find("input").removeClass("form-control-sm");
        $(`${element}_wrapper`).find("input").attr("autocomplete", "off");
        $(`${element}_wrapper`).find("select").removeClass("form-select-sm");
      },
      ...options,
    });
  }

  /**
   * Inisialisasi datatable.
   */
  buttonDataTable(element, options) {
    return new DataTable(element, {
      processing: true,
      scrollX: true,
      fixedHeader: true,
      pageLength: 10,
      language: {
        search: "",
        searchPlaceholder: "Cari...",
        lengthMenu: "_MENU_",
        info: "_START_ - _END_ dari _TOTAL_",
        infoEmpty: "0 - 0 dari 0",
        infoFiltered: "",
        zeroRecords: "Tidak ditemukan data yang sesuai",
        emptyTable: "Tidak ada data yang tersedia",
      },
      buttons: [
        {
          extend: "excel",
          text: `<span class="material-symbols-outlined me-1">download</span> Excel`,
          exportOptions: {
            columns: ":not(.no-export)",
          },
        },
        {
          extend: "copy",
          text: `<span class="material-symbols-outlined me-1">content_copy</span> Salin`,
          exportOptions: {
            columns: ":not(.no-export)",
          },
        },
        {
          extend: "print",
          text: `<span class="material-symbols-outlined me-1">print</span> Cetak`,
          exportOptions: {
            columns: ":not(.no-export)",
          },
        },
      ],
      layout: {
        topStart: "buttons",
        topEnd: "search",
        bottomStart: "info",
        bottomEnd: "paging",
      },
      preDrawCallback: function () {
        $(`${element}_wrapper`).find("thead tr th").addClass("bg-light");
        $(`${element}_wrapper`).find("tfoot tr th").addClass("bg-light");
        $(`${element}_wrapper`).find("input").removeClass("form-control-sm");
        $(`${element}_wrapper`).find("input").addClass("w-100");
        $(`${element}_wrapper`).find("input").attr("autocomplete", "off");
        $(`${element}_wrapper`).find("select").removeClass("form-select-sm");
        $(`${element}_wrapper`).find("select").addClass("w-100");
      },
      ...options,
    });
  }

  /**
   * Inisialisasi modal datatable.
   */
  modalDataTable(element, options) {
    return new DataTable(element, {
      processing: true,
      fixedHeader: true,
      info: false,
      scrollX: false,
      scrollY: "400px",
      language: {
        search: "",
        searchPlaceholder: "Cari...",
        lengthMenu: "_MENU_",
        info: "_START_ - _END_ dari _TOTAL_",
        infoEmpty: "0 - 0 dari 0",
        infoFiltered: "",
        zeroRecords: "Tidak ditemukan data yang sesuai",
        emptyTable: "Tidak ada data yang tersedia",
      },
      lengthMenu: [
        [10, 25, 50, 100, 200],
        [10, 25, 50, 100, 200],
      ],
      layout: {
        top: "search",
        topStart: null,
        topEnd: null,
        bottom: "paging",
        bottomStart: null,
        bottomEnd: null,
      },
      preDrawCallback: function () {
        const input = $(`${element}_wrapper`).find("input");
        const tableHead = $(`${element}_wrapper`).find("thead tr th");
        const tableFooter = $(`${element}_wrapper`).find("tfoot tr th");

        tableHead.addClass("bg-light");
        tableFooter.addClass("bg-light");
        input.attr("autocomplete", "off");
        input.removeClass("form-control-sm");
        input.addClass("w-100 border");
        input.parent().addClass("w-100 mb-5");
      },
      ...options,
    });
  }

  /**
   * Format waktu
   */
  dateTimeFormat(timestamp) {
    const now = new Date(timestamp);

    let day = now.getDate();
    let month = now.getMonth();
    let year = now.getFullYear();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();

    const months = [
      "Jan",
      "Feb",
      "Mar",
      "apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ];

    month = month < 10 ? `0${month}` : month;
    day = day < 10 ? `0${day}` : day;
    hours = hours < 10 ? `0${hours}` : hours;
    minutes = minutes < 10 ? `0${minutes}` : minutes;
    seconds = seconds < 10 ? `0${seconds}` : seconds;

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  }

  /**
   * Format tanggal
   */
  dateFormat(date) {
    const now = new Date(date);
    const day = now.getDate();
    const month = now.getMonth();
    const year = now.getFullYear();

    const months = [
      "Jan",
      "Feb",
      "Mar",
      "apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ];

    return `${day} ${months[month]} ${year}`;
  }

  /**
   * user foto
   */
  userPhoto(path = null) {
    if (path === null) {
      return `${this.baseUrl}/assets/images/user.webp`;
    }

    return `${this.baseUrl}/storage/${path}`;
  }

  /**
   * Format angka (numerik).
   */
  numberFormat(number, fractionDigits = 2) {
    const intl = new Intl.NumberFormat("en-US", {
      minimumFractionDigits: fractionDigits,
      maximumFractionDigits: fractionDigits,
    });

    return intl.format(number);
  }

  /**
   * Alert confirm untuk delete.
   */
  delete(title, callback) {
    bootbox.confirm({
      onEscape: true,
      backdrop: true,
      title: `Hapus ${title}`,
      message: `Yakin ingin menghapus data ${title.toLowerCase()} ini?`,
      buttons: {
        cancel: {
          className: "btn btn-light me-2",
          label: `
            <span class="material-symbols-outlined me-1">close</span>
            <span>Batal</span>
          `,
        },
        confirm: {
          className: "btn btn-danger",
          label: `
            <span class="material-symbols-outlined me-1">delete</span>
            <span>Hapus</span>
          `,
        },
      },
      callback: (result) => callback(result),
    });
  }

  /**
   * Alert confirm untuk delete.
   */
  confirm(title, message, callback) {
    bootbox.confirm({
      onEscape: true,
      backdrop: true,
      title: title,
      message: message,
      className: "modal-bootbox",
      buttons: {
        cancel: {
          className: "btn btn-light",
          label: `
            <span class="material-symbols-outlined me-1">close</span>
            <span>Batal</span>
          `,
        },
        confirm: {
          className: "btn btn-secondary",
          label: `
            <span class="material-symbols-outlined me-1">check</span>
            <span>Ya</span>
          `,
        },
      },
      callback: (result) => callback(result),
    });
  }

  /**
   * Link storage image
   */
  storageImage(path, defaultPath = null) {
    if (path !== null) {
      return `${this.baseUrl}/storage/${path}`;
    }

    if (defaultPath !== null) {
      return `${this.baseUrl}/${defaultPath}`;
    }

    return `${this.baseUrl}/assets/images/empty.jpg`;
  }

  /**
   * Menmpilkan preloader
   */
  showPreloader() {
    $("#preloader").fadeIn();
  }

  /**
   * Menyembunyikan preloader.
   */
  hidePreloader() {
    $("#preloader").fadeOut();
  }

  /**
   * Menapilkan alert
   */
  alert(variant, message) {
    $("#alert").html(`
      <div class="alert alert-${variant} border-${variant} mb-7 shadow-sm">
        <div class="row align-items-center">
            <div class="col">${message}</div>
            <div class="col-auto">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
      </div>
    `);
  }

  /**
   * Menghapus alert
   */
  clearAlert() {
    $("#alert").empty();
  }

  /**
   * Url
   */
  url(path, params = null) {
    if (params !== null) {
      const queryString = "?" + new URLSearchParams(data).toString();

      return this.baseUrl + path + queryString;
    }

    return this.baseUrl + path;
  }

  /**
   * Update URL query string
   */
  updateQueryParams(paramsObj) {
    const url = new URL(window.location);

    Object.keys(paramsObj).forEach((key) => {
      url.searchParams.set(key, paramsObj[key]);
    });

    window.history.pushState({}, "", url);
  }

  /**
   * Clear form errors
   */
  clearFormErrors(form) {
    form.find("[data-invalid]").removeClass("is-invalid");
    form.find("[data-invalid]").addClass("border");
    form.find("[data-invalid-feedback]").text("");
  }

  /**
   * Set errors
   */
  setFormErrors(form, error) {
    Object.entries(error.errors).forEach(([field, message]) => {
      form.find(`[data-invalid="${field}"]`).removeClass("border");
      form.find(`[data-invalid="${field}"]`).addClass("is-invalid");
      form.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
    });
  }

  /**
   * Reset form
   */
  resetForm(form) {
    form.trigger("reset");
  }

  /**
   * Icon
   */
  icon(icon, size = null) {
    if (size !== null) {
      return `<span class="material-symbols-outlined ${size}">${icon}</span>`;
    }

    return `<span class="material-symbols-outlined">${icon}</span>`;
  }
}

window.app = new App();
