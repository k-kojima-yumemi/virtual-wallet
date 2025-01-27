import type { FC } from "react";

type Props = {
    onClick: () => void;
    name: string;
    id: string;
};

export const Button: FC<Props> = ({ id, name, onClick }) => {
    return (
        <div className="inner-flex">
            <button
                className="circle btn"
                type="button"
                id={id}
                onClick={onClick}
            >
                {name}
            </button>
        </div>
    );
};
