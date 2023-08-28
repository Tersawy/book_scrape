import "./bootstrap";

let isLoading = false;

window.addEventListener("load", function () {
    // Initial load
    fetchMoreBooks();
});

window.addEventListener("scroll", () => {
    if (isFetchAvailabe()) fetchMoreBooks();
});

function isFetchAvailabe() {
    const windowHeight = window.innerHeight;
    const documentHeight = document.body.offsetHeight;
    const scrollPosition = window.scrollY;

    return scrollPosition + windowHeight >= documentHeight - 100;
}

function setLoading(value) {
    isLoading = value;

    let loadingItem = document.getElementById("loading");

    loadingItem.classList.replace(
        value ? "d-none" : "d-flex",
        value ? "d-flex" : "d-none"
    );
}

async function fetchMoreBooks() {
    if (isLoading) return;

    setLoading(true);

    try {
        let res = await axios.get("/api/books");

        if (res.data.error) {
            fireGlobalMsg(res.data.error);
        }

        const books = res.data.books;

        if (books.length > 0) {
            const booksContainer = document.querySelector(".books");

            books.forEach((book) =>
                booksContainer.appendChild(createBookCard(book))
            );

            setLoading(false);

            if (isFetchAvailabe()) fetchMoreBooks();
        } else {
            // No more books to load
            setLoading(false);
        }
    } catch (e) {
        setLoading(false);

        fireGlobalMsg(e.response.data.message);
    }
}

function createBookCard(book) {
    const card = el("div", "col mb-4", {});
    const bookCard = el("div", "book_card card border-0 shadow-sm px-3 pt-3", {
        dir: "rtl",
    });

    const title = el("h5", "book_card_title", {}, [book.title]);
    const author = el("div", "book_card_author fw-bold text-primary", {}, [
        book.author,
    ]);

    const footer = el(
        "div",
        "book_card_footer d-flex align-items-center justify-content-between text-muted py-2",
        {}
    );

    if (!isNullOrUndefinedOrEmpty(book.lang)) {
        const langDiv = el("div", "d-flex flex-column", {});
        langDiv.appendChild(el("span", "", {}, ["اللغه"]));
        langDiv.appendChild(el("div", "", {}, [book.lang]));
        footer.appendChild(langDiv);
    }
    if (!isNullOrUndefinedOrEmpty(book.size)) {
        const sizeDiv = el("div", "d-flex flex-column", {});
        sizeDiv.appendChild(el("span", "", {}, ["الحجم"]));
        sizeDiv.appendChild(el("div", "", {}, [book.size]));
        footer.appendChild(sizeDiv);
    }
    if (!isNullOrUndefinedOrEmpty(book.pages_count)) {
        const pagesDiv = el("div", "d-flex flex-column align-items-center", {});
        pagesDiv.appendChild(el("span", "", {}, ["الصفحات"]));
        pagesDiv.appendChild(el("div", "", {}, [book.pages_count]));
        footer.appendChild(pagesDiv);
    }

    const controlArea = el(
        "div",
        "book_card_area_control align-items-center justify-content-center",
        {}
    );
    if (!isNullOrUndefinedOrEmpty(book.download_link)) {
        const downloadLink = el(
            "a",
            "btn btn-primary text-white rounded-circle",
            { href: book.download_link, download: "" }
        );
        downloadLink.appendChild(el("i", "fa fa-download"));
        controlArea.appendChild(downloadLink);
    }

    bookCard.appendChild(title);
    bookCard.appendChild(author);

    if (footer.children.length > 0) {
        const hr = el("hr", "mb-0", {});
        bookCard.appendChild(hr);
        bookCard.appendChild(footer);
    }

    if (controlArea.children.length > 0) {
        bookCard.appendChild(controlArea);
    }

    card.appendChild(bookCard);

    return card;
}

function isNullOrUndefinedOrEmpty(value) {
    return value === undefined || value === null || value === "";
}

function el(elementName, classNames, attributes = {}, children = []) {
    const element = document.createElement(elementName);

    if (classNames) {
        element.classList.add(...classNames.split(" "));
    }

    for (const [key, value] of Object.entries(attributes)) {
        element.setAttribute(key, value);
    }

    for (const child of children) {
        if (child instanceof Node) {
            element.appendChild(child);
        } else if (typeof child === "string") {
            element.appendChild(document.createTextNode(child));
        }
    }

    return element;
}

function fireGlobalMsg(msg) {
    const globalMsg = document.getElementById("globalMsg");
    globalMsg.textContent = msg;
    const toastLive = document.getElementById("liveToast");
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLive);
    toastBootstrap.show();
}

window.fireGlobalMsg = fireGlobalMsg;
