const dialog = options => {

    const dialogWrapper = document.createElement('div');
    const dialogBox = document.createElement('div');
    const message = document.createElement('div');
    const buttons = document.createElement('div');

    message.appendChild(document.createTextNode(options.message));

    Object.keys(options.buttons).forEach(key => {
        const button = document.createElement('button');
        button.onclick = () => {
            if (key in options.callbacks) options.callbacks[key]();
            dialogWrapper.parentNode.removeChild(dialogWrapper);
        };
        button.appendChild(document.createTextNode(options.buttons[key]?.text || key.charAt(0).toUpperCase() + key.toLowerCase().slice(1)));
        button.classList.add(...(options.buttons[key]?.class?.split(' ') || ['__dialog-button', '__dialog-button-' + key.toLowerCase()]));
        buttons.appendChild(button);
    });

    dialogWrapper.classList.add(...options.wrapperClass.split(' '));
    dialogBox.classList.add(...options.boxClass.split(' '));
    message.classList.add(...options.messageClass.split(' '));
    buttons.classList.add(...options.buttonsClass.split(' '));

    dialogWrapper.appendChild(dialogBox);
    dialogBox.appendChild(message);
    dialogBox.appendChild(buttons);

    document.body.appendChild(dialogWrapper);

    document.activeElement.blur();

};

export default dialog;