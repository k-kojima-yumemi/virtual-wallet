import {type FC, useEffect, useState} from "react";

type Props = {
    onHasBalance?: () => void;
    onHasNoBalance?: () => void;
};

type BalanceApi = {
    balance: number;
};

export const Balance: FC<Props> = ({onHasBalance, onHasNoBalance}) => {
    const [balance, setBalance] = useState<number>();
    useEffect(() => {
        fetch("/api/balance")
            .then((response) => response.json())
            .then((json: BalanceApi) => {
                console.log(json);
                setBalance(json.balance);
                if (json.balance > 0 && onHasBalance) {
                    onHasBalance();
                }
                if (json.balance <= 0 && onHasNoBalance) {
                    onHasNoBalance();
                }
            });
    }, [onHasBalance, onHasNoBalance]);

    if (balance === undefined) {
        return <div className="min-h-20"/>;
    }
    return (
        <div className="min-h-20 text-center text-6xl">
            {formatBalance(balance)}
        </div>
    );
};

function formatBalance(balance: number) {
    const formatter = new Intl.NumberFormat("ja-JP", {
        style: "currency",
        currency: "JPY",
        currencyDisplay: "name",
    });
    return formatter.format(balance);
}
