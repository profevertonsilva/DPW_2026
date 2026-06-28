<style>
    .comunicacao-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 24px;
        min-height: 600px;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 100;
        margin-right: 0;
        max-width: 100%;
        margin-left: 280px;
        margin-top:100px;
    }

    .comunicacao-body {
        display: flex;
        gap: 24px;
        overflow: hidden;
        flex: 1;
        min-height: 0;
    }

    .conversas-list {
        width: 300px;
        min-width: 300px;
        border-right: 1px solid #e0e0e0;
        padding-right: 24px;
        overflow-y: auto;
        flex-shrink: 0;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-width: 0;
    }

    .comunicacao-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e0e0e0;
    }

    .comunicacao-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #4F4F4F;
        margin: 0;
    }

    .conversas-list h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: #4F4F4F;
        margin: 0 0 16px 0;
    }

    .conversa-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.2s;
        margin-bottom: 8px;
    }

    .conversa-item:hover {
        background: #f5f5f5;
    }

    .conversa-item.active {
        background: #e8f5ef;
    }

    .conversa-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #6FCF97;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 18px;
    }

    .conversa-info {
        flex: 1;
    }

    .conversa-info h4 {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #4F4F4F;
        margin: 0 0 4px 0;
    }

    .conversa-info p {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #888;
        margin: 0;
    }

    .conversa-badge {
        background: #6FCF97;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 16px;
    }

    .chat-header-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #6FCF97;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 18px;
    }

    .chat-header-info h4 {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: #4F4F4F;
        margin: 0 0 4px 0;
    }

    .chat-header-info p {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #888;
        margin: 0;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 16px;
    }

    .message {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }

    .message.received {
        flex-direction: row;
    }

    .message.sent {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #6FCF97;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }

    .message.sent .message-avatar {
        background: #56CCF2;
    }

    .message-content {
        max-width: 70%;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #4F4F4F;
        line-height: 1.5;
    }

    .message.received .message-bubble {
        background: #fff;
        border: 1px solid #e0e0e0;
    }

    .message.sent .message-bubble {
        background: #6FCF97;
        color: #fff;
    }

    .message-time {
        font-family: 'Inter', sans-serif;
        font-size: 11px;
        color: #888;
        margin-top: 4px;
    }

    .message.sent .message-time {
        text-align: right;
    }

    .chat-input {
        display: flex;
        gap: 12px;
    }

    .chat-input input {
        flex: 1;
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 24px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #4F4F4F;
        outline: none;
        transition: border-color 0.2s;
    }

    .chat-input input:focus {
        border-color: #6FCF97;
    }

    .chat-input button {
        background: #6FCF97;
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-input button:hover {
        background: #58b87e;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        color: #888;
        text-align: center;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        color: #e0e0e0;
    }

    .empty-state h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: #888;
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #888;
        margin: 0;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="comunicacao-container">
                <div class="comunicacao-header">
                    <h2>Comunicação Interna</h2>
                    <button class="btn-agendar" style="background: #6FCF97; border: none; border-radius: 12px; padding: 8px 16px; color: #fff; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif;">+ Nova Conversa</button>
                </div>
                <div class="comunicacao-body">
                    <div class="conversas-list">
                        <h3>Conversas</h3>
                        
                        <div class="conversa-item active">
                            <div class="conversa-avatar">MA</div>
                            <div class="conversa-info">
                                <h4>Maria Silva</h4>
                                <p>Administrador</p>
                            </div>
                            <span class="conversa-badge">3</span>
                        </div>

                        <div class="conversa-item">
                            <div class="conversa-avatar">JO</div>
                            <div class="conversa-info">
                                <h4>João Santos</h4>
                                <p>Veterinário</p>
                            </div>
                        </div>

                        <div class="conversa-item">
                            <div class="conversa-avatar">AN</div>
                            <div class="conversa-info">
                                <h4>ONG AmigoPet</h4>
                                <p>Organização</p>
                            </div>
                            <span class="conversa-badge">1</span>
                        </div>

                        <div class="conversa-item">
                            <div class="conversa-avatar">PE</div>
                            <div class="conversa-info">
                                <h4>Pedro Costa</h4>
                                <p>Rastreador</p>
                            </div>
                        </div>

                        <div class="conversa-item">
                            <div class="conversa-avatar">LU</div>
                            <div class="conversa-info">
                                <h4>Lucas Oliveira</h4>
                                <p>Voluntário</p>
                            </div>
                        </div>
                    </div>

                    <div class="chat-area">
                        <div class="chat-header">
                            <div class="chat-header-avatar">MA</div>
                            <div class="chat-header-info">
                                <h4>Maria Silva</h4>
                                <p>Administrador • Online</p>
                            </div>
                        </div>

                        <div class="chat-messages">
                            <div class="message received">
                                <div class="message-avatar">MA</div>
                                <div class="message-content">
                                    <div class="message-bubble">Olá! Como está o andamento dos procedimentos hoje?</div>
                                    <div class="message-time">14:30</div>
                                </div>
                            </div>

                            <div class="message sent">
                                <div class="message-avatar">EU</div>
                                <div class="message-content">
                                    <div class="message-bubble">Tudo bem! Já agendei 3 consultas para esta tarde.</div>
                                    <div class="message-time">14:32</div>
                                </div>
                            </div>

                            <div class="message received">
                                <div class="message-avatar">MA</div>
                                <div class="message-content">
                                    <div class="message-bubble">Perfeito! Precisa de alguma ajuda com algum caso específico?</div>
                                    <div class="message-time">14:33</div>
                                </div>
                            </div>

                            <div class="message sent">
                                <div class="message-avatar">EU</div>
                                <div class="message-content">
                                    <div class="message-bubble">Não por enquanto, está tudo sob controle. Obrigado!</div>
                                    <div class="message-time">14:35</div>
                                </div>
                            </div>
                        </div>

                        <div class="chat-input">
                            <input type="text" placeholder="Digite sua mensagem...">
                            <button>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
