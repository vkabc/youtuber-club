<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {onMounted, ref} from "vue";
import {HashConnect, HashConnectTypes, MessageTypes} from 'hashconnect';
import {
    AccountBalanceQuery,
    AccountId,
    Client,
    PrivateKey, TokenAssociateTransaction,
    TokenCreateTransaction,
    TokenMintTransaction,
    TokenSupplyType,
    TokenType, Transaction, TransactionId, TransferTransaction
} from "@hashgraph/sdk";
import axios, {create} from "axios";


const isLoading = ref(false)
const amount = ref(0)

interface User  {
    id: string,
    name: string,
    quantity: string
    cid: string,
    img: string
}
const props = defineProps<{
    id: string,
    user: User
}>()
const img = ref("")

const form = ref({
    discord_id: "",
})

const selectedToken = ref()
const tokens = ref([])
const random = ref("")
const topicCached = ref("")
let hashconnect;
const delay = ms => new Promise(res => setTimeout(res, ms));
const connectWallet = async () => {

    hashconnect = new HashConnect(true);
     // hashconnect.clearConnectionsAndData();





    let appMetadata: HashConnectTypes.AppMetadata = {
        name: "dApp Example",
        description: "An example hedera dApp",
        icon: "https://absolute.url/to/icon.png"
    }

    hashconnect.foundExtensionEvent.on((data) => {
        console.log("Found extension", data);
        //this.availableExtension = data;
    })

    //This is fired when a wallet approves a pairing
    hashconnect.pairingEvent.on((data) => {
        console.log("Paired with wallet", data);

        //this.pairingData = data.pairingData!;
    });

    let initData = await hashconnect.init(appMetadata, "testnet", false);
    //This is fired when HashConnect loses connection, pairs successfully, or is starting connection
    hashconnect.connectionStatusChangeEvent.on((state) => {
        console.log("hashconnect state change event", state);
        //this.state = state;
    })

    hashconnect.connectToLocalWallet();
    random.value = initData.savedPairings[0].accountIds[0];
    console.log(random)
    topicCached.value = initData.topic;

    const url = "https://api-lb.hashpack.app/mirror";
    const result = await axios.post(url, {
        "network" : "testnet",
        "endpoint": `api/v1/accounts/${random.value}?transactionType=UNCHECKEDSUBMIT`

    })
    tokens.value = result.data.balance.tokens;




}

const aliceId = AccountId.fromString(import.meta.env.VITE_ALICE_ACCOUNT_ID);
const aliceKey = PrivateKey.fromString(import.meta.env.VITE_ALICE_PRIVATE_KEY);
// const bobId = AccountId.fromString(import.meta.env.VITE_BOB_ACCOUNT_ID);
// const bobKey = PrivateKey.fromString(import.meta.env.VITE_BOB_PRIVATE_KEY);

async function makeBytes(trans: Transaction, signingAcctId: string) {
    let transId = TransactionId.generate(signingAcctId)
    trans.setTransactionId(transId);
    trans.setNodeAccountIds([new AccountId(3)]);

    await trans.freeze();

    let transBytes = trans.toBytes();

    return transBytes;
}
const client = Client.forTestnet().setOperator(aliceId, aliceKey);
async function sendTransaction(trans: Uint8Array, acctToSign: string, return_trans: boolean = false, hideNfts: boolean = false, getRecord: boolean = false) {
    const transaction: MessageTypes.Transaction = {
        topic:topicCached.value,
        byteArray: trans,

        metadata: {
            accountToSign: acctToSign,
            returnTransaction: return_trans,
            hideNft: hideNfts,
            getRecord: getRecord
        }
    }

    return await hashconnect.sendTransaction(topicCached.value, transaction)
}
async function createNonFungibleToken() {
    isLoading.value = true;

    axios.post(route('edit'), {
        'nft_id': selectedToken.value,
        'discord_id': form.value.discord_id,

    })


    window.location.href = route('success');

}

</script>

<template>

    <GuestLayout>

        <div class="py-12">

            <div class="flex">


<!--                <button class="bg-amber-400" @click="createNonFungibleToken">-->
<!--                    Please Work-->
<!--                </button>-->

            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                {{random}}

                <button v-show="!random" @click='connectWallet' type="button" class="mb-4 text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 ">Connect to HashPack</button>

                <div class="text-2xl font-semibold">

                </div>
                <div class="bg-white  shadow-sm sm:rounded-lg">


                    <div class="text-gray-600 tracking-widest" >
                    </div>
                    <div class="flex align-center">


                        <p class="tracking-widest text-gray-500 md:text-lg dark:text-gray-400 mb-6">{{name}}</p>


                    </div>


                    <form class="text-left">
                        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an option</label>
                        <select id="countries" v-model="selectedToken" class="mb-4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option v-for="token in tokens"  :value="token.token_id">{{token.token_id}}</option>
                        </select>

                        <div class="mb-6">
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discord ID</label>
                            <input v-model="form.discord_id" type="text" id="text"
                                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
                                   placeholder="yuser-discord#22" required>
                        </div>
<!--                        <div class="mb-6">-->
<!--                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Card-->
<!--                                Number</label>-->
<!--                            <input v-model="form.cardNumber" type="text" id="text"-->
<!--                                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"-->
<!--                                   required>-->
<!--                        </div>-->
<!--                        <div class="mb-6">-->
<!--                            <label for="repeat-password"-->
<!--                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CVV</label>-->
<!--                            <input v-model="form.cvv" type="text" id="repeat-password"-->
<!--                                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"-->
<!--                                   required>-->
<!--                        </div>-->

<!--                        <div class="mb-6">-->
<!--                            <label for="repeat-password"-->
<!--                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expiry</label>-->
<!--                            <input v-model="form.expiry" type="month" id="repeat-password"-->
<!--                                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"-->
<!--                                   required>-->
<!--                        </div>-->

                        <button type="button" @click="createNonFungibleToken" v-show="random"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <div class="flex">

                                {{
                                    isLoading ? "Minting ...  " : "Set Discord ID"
                                }}

                                <svg v-show="isLoading" aria-hidden="true"
                                     class="ml-2 w-5 h-5 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor"/>
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill"/>
                                </svg>
                            </div>
                        </button>

                    </form>


                </div>

            </div>
        </div>
    </GuestLayout>
</template>
