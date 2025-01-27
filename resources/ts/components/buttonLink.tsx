import type { FC } from "react";

type Props = {
    destination: string;
    name: string;
    id: string;
    enabled: boolean;
};

export const LinkButton: FC<Props> = ({ id, name, destination, enabled }) => {
    if (enabled) {
        return (
            <div className="flex justify-center">
                <a
                    className="w-50 rounded-3xl bg-teal-300 p-4 text-center text-2xl"
                    id={id}
                    href={destination}
                >
                    {name}
                </a>
            </div>
        );
    }
    return (
        <div className="flex justify-center">
            <span
                className="w-50 rounded-3xl bg-gray-400 p-4 text-center text-2xl"
                id={id}
            >
                {name}
            </span>
        </div>
    );
};
