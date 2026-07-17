let source = '';
const created = [];

class FakeChart {
    constructor(context, config) {
        if (!context) {
            throw new Error('Canvas sem contexto.');
        }

        created.push(config.type);
    }
}

FakeChart.defaults = { font: {} };
global.window = { Chart: FakeChart };
global.getComputedStyle = () => ({
    getPropertyValue(name) {
        if (name.includes('destaque') || name.includes('secundaria')) {
            return '#B40404';
        }

        return '#FFFFFF';
    }
});

const elements = {};
global.document = {
    documentElement: {},
    body: { dataset: { identidade: 'netaki' } },
    getElementById(id) {
        if (!elements[id]) {
            elements[id] = {
                hidden: false,
                parentElement: {
                    querySelector() {
                        return null;
                    },
                    appendChild() {}
                },
                getContext() {
                    return { canvas: this };
                }
            };
        }

        return elements[id];
    },
    createElement() {
        return { className: '', innerHTML: '' };
    }
};

process.stdin.setEncoding('utf8');
process.stdin.on('data', chunk => {
    source += chunk;
});
process.stdin.on('end', () => {
    eval(source);

    if (created.length !== 4) {
        throw new Error(`Esperados 4 gráficos; recebidos ${created.length}.`);
    }

    process.stdout.write(
        `graficos_criados=${created.length} tipos=${created.join(',')}\n`
    );
});
