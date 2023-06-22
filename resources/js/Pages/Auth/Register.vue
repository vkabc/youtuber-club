<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {Buffer} from 'buffer';

import {
    AccountId,
    PrivateKey,
    Client,
    TokenCreateTransaction,
    TokenType,
    TokenSupplyType,
    TokenMintTransaction, AccountBalanceQuery, TokenAssociateTransaction, TransferTransaction
} from "@hashgraph/sdk";
import {onMounted} from "vue";

const form = useForm({
    name: '',
    address: '',
    quantity: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const aliceId = AccountId.fromString(import.meta.env.VITE_ALICE_ACCOUNT_ID);
const aliceKey = PrivateKey.fromString(import.meta.env.VITE_ALICE_PRIVATE_KEY);
const bobId = AccountId.fromString(import.meta.env.VITE_BOB_ACCOUNT_ID);
const bobKey = PrivateKey.fromString(import.meta.env.VITE_BOB_PRIVATE_KEY);

const client = Client.forTestnet().setOperator(aliceId, aliceKey);

console.log(client);

async function createNonFungibleToken() {
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
}

onMounted(() => {
    createNonFungibleToken();
});
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="NFT Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Wallet" />

                <TextInput
                    id="email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Quantity" />

                <TextInput
                    id="email"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.quantity"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>




            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Mint NFTs
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
