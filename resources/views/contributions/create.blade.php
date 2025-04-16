@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">Contribuer au projet</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3>{{ $project->title }}</h3>
                        <p class="text-muted">{{ $project->short_description }}</p>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>Objectif:</strong> {{ number_format($project->funding_goal, 2, ',', ' ') }} €
                            </div>
                            <div>
                                <strong>Collecté:</strong> {{ number_format($project->current_amount, 2, ',', ' ') }} €
                            </div>
                            <div>
                                <strong>Catégorie:</strong> {{ $project->category->name }}
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('contributions.store', $project->slug) }}" id="payment-form">
                        @csrf
                        
                        <div class="mb-4">
                            <h4>Montant de votre contribution</h4>
                            
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" min="1" step="0.01" class="form-control form-control-lg @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 20) }}" required>
                                </div>
                                @error('amount')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Minimum: 1€</small>
                            </div>
                            
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-outline-success amount-btn" data-amount="5">5€</button>
                                <button type="button" class="btn btn-outline-success amount-btn" data-amount="10">10€</button>
                                <button type="button" class="btn btn-outline-success amount-btn" data-amount="20">20€</button>
                                <button type="button" class="btn btn-outline-success amount-btn" data-amount="50">50€</button>
                                <button type="button" class="btn btn-outline-success amount-btn" data-amount="100">100€</button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h4>Méthode de paiement</h4>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }} checked>
                                    <label class="form-check-label" for="credit_card">
                                        Carte de crédit
                                    </label>
                                </div>
                                
                                <div id="credit-card-details" class="border rounded p-3 mb-3">
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">Numéro de carte</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="•••• •••• •••• ••••" disabled>
                                        <small class="text-muted">Ceci est une démo, aucune information de carte n'est nécessaire</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry" class="form-label">Date d'expiration</label>
                                            <input type="text" class="form-control" id="expiry" placeholder="MM/AA" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" placeholder="123" disabled>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paypal">
                                        PayPal
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h4>Récapitulatif</h4>
                            
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Contribution au projet</span>
                                    <span id="summary-amount">20,00 €</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Frais de transaction</span>
                                    <span>0,00 €</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span id="summary-total">20,00 €</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> et je comprends que cette contribution soutient le projet sans garantie de contrepartie commerciale.
                                </label>
                                @error('terms')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Confirmer ma contribution</button>
                            <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des boutons de montant prédéfinis
        const amountBtns = document.querySelectorAll('.amount-btn');
        const amountInput = document.getElementById('amount');
        const summaryAmount = document.getElementById('summary-amount');
        const summaryTotal = document.getElementById('summary-total');
        
        function updateSummary() {
            const amount = parseFloat(amountInput.value) || 0;
            summaryAmount.textContent = amount.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
            summaryTotal.textContent = amount.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }
        
        amountBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                amountInput.value = amount;
                updateSummary();
            });
        });
        
        amountInput.addEventListener('input', updateSummary);
        
        // Initialisation du récapitulatif
        updateSummary();
    });
</script>
@endsection