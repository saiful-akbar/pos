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
      },
      buttons: [
        {
          extend: "excel",
          text: `<span class="material-symbols-outlined me-1">download</span> Excel`,
        },
        {
          extend: "copy",
          text: `<span class="material-symbols-outlined me-1">content_copy</span> Salin`,
        },
        {
          extend: "print",
          text: `<span class="material-symbols-outlined me-1">print</span> Cetak`,
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
      scrollY: "450px",
      language: {
        search: "",
        searchPlaceholder: "Cari...",
        lengthMenu: "_MENU_",
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
    const day = now.getDate();
    const month = now.getMonth();
    const year = now.getFullYear();

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

    hours = hours < 10 ? `0${hours}` : hours;
    minutes = minutes < 10 ? `0${minutes}` : minutes;
    seconds = seconds < 10 ? `0${seconds}` : seconds;

    return `${day} ${months[month]} ${year}, ${hours}:${minutes}:${seconds}`;
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
  numberFormat(number) {
    const intl = new Intl.NumberFormat("id-ID", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
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
      <div class="alert alert-${variant} border-${variant}  mb-6">
        <div class="row align-items-center">
            <div class="col">
                ${message}
            </div>

            <div class="col-auto">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
      </div>
    `);
  }
}

window.app = new App();
