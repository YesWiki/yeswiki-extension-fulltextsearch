export type Engine = {
    driver: string;
    typesense_config?: {
        api_key: string;
        host: string;
        port: number;
        protocol: string;
    };
}

export const engineProvider: Engine[] = [
    {
        driver: 'loupe'
    },
    {
        driver: 'typesense',
        typesense_config: {
            api_key: 'xyz',
            host: 'typesense',
            port: 8108,
            protocol: 'http',
        }
    }
];
