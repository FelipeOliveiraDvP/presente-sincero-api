const moneyFormat = (value) => {
    var formatter = new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    });

    return formatter.format(value);
};

export default moneyFormat;
