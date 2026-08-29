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
   *
   * @param {element} element
   * @param {object} options
   * @returns
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
        $(`${element}_wrapper`).find("input").addClass("border");
        $(`${element}_wrapper`).find("input").attr("autocomplete", "off");
        $(`${element}_wrapper`).find("select").removeClass("form-select-sm");
        $(`${element}_wrapper`).find("select").addClass("border");
      },
      ...options,
    });
  }

  /**
   * Format waktu
   *
   * @param {string} timestamp
   * @returns string
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
   * user foto
   *
   * @param {string} path
   * @returns
   */
  userPhoto(path = null) {
    if (path === null) {
      return `${this.baseUrl}/assets/images/user.webp`;
    }

    return `${this.baseUrl}/storage/${path}`;
  }

  /**
   * Format angka (numerik).
   *
   * @param {int} number
   * @returns
   */
  numberFormat(number) {
    return number.toLocaleString("id-ID");
  }

  delete(title, callback) {
    bootbox.confirm({
      onEscape: true,
      backdrop: true,
      title: `Hapus ${title}`,
      message: `Yakin ingin menghapus data ${title} ini?`,
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
}

window.app = new App();
