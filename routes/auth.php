<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
//     // Part 1 - Create token
//
//     let nftCreate = await new TokenCreateTransaction()
//         .setTokenName("Alice NFT")
//         .setTokenSymbol("ANFT")
//         .setTokenType(TokenType.NonFungibleUnique)
//         .setDecimals(0)
//         .setInitialSupply(0)
//         .setTreasuryAccountId(aliceId)
//         .setSupplyType(TokenSupplyType.Finite)
//         .setMaxSupply(10)
//         .setSupplyKey(aliceKey)
//         .freezeWith(client);
//
//     let nftCreateTxSign = await nftCreate.sign(aliceKey);
//     let nftCreateSubmit = await nftCreateTxSign.execute(client);
//     let nftCreateRx = await nftCreateSubmit.getReceipt(client);
//     let tokenId = nftCreateRx.tokenId;
//     console.log(`- Created NFT with Token ID: ${tokenId} \n`);
//     // Part 2 - Mint token
//
//     const CID = "ipfs://Qme5niYwb7b9dFimAgJwWYFUbxJ2rbM5MguV32Z4UuALmg/alice-nft-metadata.json";
//
//     let mintTx = await new TokenMintTransaction()
//         .setTokenId(tokenId)
//         .setMetadata([Buffer.from(CID)])
//         .freezeWith(client);
//
//     let mintTxSign = await mintTx.sign(aliceKey);
//     let mintTxSubmit = await mintTxSign.execute(client);
//     let mintRx = await mintTxSubmit.getReceipt(client);
//     console.log(`- Created NFT ${tokenId} with serial: ${mintRx.serials[0].low} \n`);
//
//     // Part 3 - Associate token to user account
//     let associateAliceTx = await new TokenAssociateTransaction().setAccountId(bobId).setTokenIds([tokenId]).freezeWith(client).sign(bobKey);
//     let associateAliceTxSubmit = await associateAliceTx.execute(client);
//     let associateAliceRx = await associateAliceTxSubmit.getReceipt(client);
//     console.log(`- NFT association with Bob's account: ${associateAliceRx.status}\n`);
//
//     // Part 4 - Transfer token
// // balance check (before)
//     var balanceCheckTx = await new AccountBalanceQuery().setAccountId(aliceId).execute(client);
//     console.log(`- Alice balance: ${balanceCheckTx.tokens._map.get(tokenId.toString())} NFTs of ID ${tokenId}`);
//     var balanceCheckTx = await new AccountBalanceQuery().setAccountId(bobId).execute(client);
//     console.log(`- Bob's balance: ${balanceCheckTx.tokens._map.get(tokenId.toString())} NFTs of ID ${tokenId}`);
//
//     let tokenTransferTx = await new TransferTransaction().addNftTransfer(tokenId, 1, aliceId, bobId).freezeWith(client).sign(aliceKey);
//     let tokenTransferSubmit = await tokenTransferTx.execute(client);
//     let tokenTransferRx = await tokenTransferSubmit.getReceipt(client);
//     console.log(`\n- NFT transfer from Treasury to Alice: ${tokenTransferRx.status} \n`);
//
//     // balance check (after)
//     var balanceCheckTx = await new AccountBalanceQuery().setAccountId(aliceId).execute(client);
//     console.log(`- Alice balance: ${balanceCheckTx.tokens._map.get(tokenId.toString())} NFTs of ID ${tokenId}`);
//     var balanceCheckTx = await new AccountBalanceQuery().setAccountId(bobId).execute(client);
//     console.log(`- Bob's balance: ${balanceCheckTx.tokens._map.get(tokenId.toString())} NFTs of ID ${tokenId}`);
//
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
