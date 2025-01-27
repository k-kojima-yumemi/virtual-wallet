import "../css/app.css";
import {type FC, useState} from "react";
import {createRoot} from "react-dom/client";
import {Balance} from "./components/balance";
import {BalanceHeader} from "./components/balanceHeader";
import {LinkButton} from "./components/buttonLink";

const App: FC = () => {
    const [canUse, setCanUse] = useState(false);
    const onBalanceChange = useCallback((balance: number) => {
        setCanUse(balance > 0);
    }, []);

    return (
        <div
            id="showBalanceContainer"
            className="flex flex-col items-center gap-4"
        >
            <BalanceHeader/>
            <Balance onBalanceChange={onBalanceChange}/>
            <div className="wrap">
                <LinkButton
                    destination="/logs"
                    name="使用履歴"
                    id="logsButton"
                    enabled={true}
                />
            </div>
            <div className="flex flex-row justify-center gap-4">
                <LinkButton
                    destination="/charge"
                    name="チャージ"
                    id="chargeButton"
                    enabled={true}
                />
                <LinkButton
                    destination="/use"
                    name="使用"
                    id="useButton"
                    enabled={canUse}
                />
            </div>
        </div>
    );
};

const reactRootElement = document.getElementById("react-root");
if (reactRootElement) {
    const root = createRoot(reactRootElement);
    root.render(<App/>);
}
