const applyStyles = options => {

    const style = document.createElement('style');

    style.appendChild(document.createTextNode(`
    ${options.wrapper ? `.__dialog-wrapper {
        position: fixed;
        z-index: 10;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,${options.darken});
    }` : ''}
    
    ${options.default ? `.__dialog-box {
        font-family: Inter, Avenir, Helvetica, Arial, sans-serif;
        font-size: 16px;
        line-height: 24px;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.87);
        background-color: rgb(51, 50, 50);
        width: fit-content;
        height: fit-content;
        margin: 20% auto;
        padding: 20px;
        text-align: center;
        border-radius: 10px;
    }
    
    .__dialog-message {
        margin-bottom: 20px;
    }
    
    .__dialog-buttons {
        display: flex;
        justify-content: space-between;
        gap: 50px;
    }
    
    .__dialog-button  {
        color: rgba(255, 255, 255, 0.87);
        border-radius: 8px;
        border: 1px solid transparent;
        padding: 0.6em 1.2em;
        font-size: 1em;
        font-weight: 500;
        font-family: inherit;
        background-color: #1a1a1a;
        cursor: pointer;
        transition: border-color 0.25s;
    }
    
    .__dialog-button-confirm {
        background-color: #535bf2;
    }

    .__dialog-button-delete {
        --colour: #f22e1f;
        background-color: var(--colour);
        border-color: var(--colour);
    }

    .__dialog-button:hover {
        border-color: #646cff;
    }

    .__dialog-button-delete:hover, .__dialog-button-confirm:hover {
        border-color: white;
    }

    .__dialog-button:focus,
    .__dialog-button:focus-visible {
        outline: 4px auto -webkit-focus-ring-color;
    }` : ''}
    `));

    document.head.append(style);

};

export default applyStyles;