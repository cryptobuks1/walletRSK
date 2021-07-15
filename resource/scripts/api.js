async function getBalanceRBTC(chainId, address, APIKEY) {
	let url;
	if(chainId == 30){		
		url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/?primer=%5B%7B%22%24match%22%3A%7B%22contract_name%22%3A%22RSK+Mainnet+Ether%22%2C+%22contract_ticker_symbol%22%3A+%22RBTC%22%7D%7D%5D&key=${APIKEY}`);
	} else if(chainId == 31){
		url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/?primer=%5B%7B%22%24match%22%3A%7B%22contract_name%22%3A%22RSK+Testnet+Ether%22%2C+%22contract_ticker_symbol%22%3A+%22tRBTC%22%7D%7D%5D&key=${APIKEY}`);
	}

  	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;


  	if (result.error) {
	    returnedData = result; 
	} else {
		// result.btc_price = await getCoinPrice("btc");
		returnedData = result; 
	}

  	return returnedData;
}


async function getFeeRBTC(chainId, address, APIKEY) {

	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/transactions_v2/?page-size=100000&key=${APIKEY}`);

  	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;


  	if (result.error) {
	    returnedData = result; 
	} else {
		let feeTotal = 0;
		let expensesTotal = 0;
		let incomeTotal = 0;

		let transactions = result.data.items;

		for(var id in transactions){
			if(transactions[id].from_address.toLowerCase() == address.toLowerCase()){
				feeTotal += transactions[id].gas_spent * (transactions[id].gas_price / 10 ** (18) );
				expensesTotal += transactions[id].value / 10 ** 18;
			}
			else if(transactions[id].to_address.toLowerCase() == address.toLowerCase()){
				incomeTotal += transactions[id].value / 10 ** 18;
			}
		}
		returnedData = result; 
		returnedData.feeTotal = feeTotal;
		returnedData.expensesTotal = expensesTotal;
		returnedData.incomeTotal = incomeTotal;

	}

  	return returnedData;
}


async function getGasPrice() {
	const gasPrice = document.getElementById("gasPrice");

	;(async () => {
		const priceBTC = await getCoinPrice("btc", "ckey_46ed80647b0c42738fc8dabde59");

		const rawResponse = await fetch("https://public-node.testnet.rsk.co", {
		 	body: `{"jsonrpc":"2.0","method":"eth_getBlockByNumber","params":["latest",false],"id":1}`,
		  	headers: {
		    	"Content-Type": "application/json"
		  	},
		  	method: "POST"
		})
	  	const content = await rawResponse.json();

	  	gasPrice.innerHTML = "~ " + (parseInt(content.result.minimumGasPrice)/10**9);

	})();
}


async function getCoinPrice(coin, APIKEY) {
	switch (coin) {
	  	case "btc":
	   		let response = await fetch(`https://api.covalenthq.com/v1/1/address/0x2260fac5e5542a773aa44fbcfedf7c193bc2c599/balances_v2/?primer=%5B%7B%22%24match%22%3A%7B%22contract_address%22%3A%220x2260fac5e5542a773aa44fbcfedf7c193bc2c599%22%7D%7D%5D&key=${APIKEY}`);
		  	let result = await response.json();
		  	return result.data.items[0].quote_rate;
	   	break;
	  	case "eth":

	   		break;
	}
}


async function readMetadataJSON(){
	let response = await fetch("/resource/tokensInfo/metadata.json");
  	let result = await response.json();
  	return result;
}


async function getTokenMetadata(chainId, address, contract, APIKEY) {
	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/?primer=%5B%7B%22%24match%22%3A%7B%22contract_address%22%3A+%22${contract}%22%7D%7D%5D&key=${APIKEY}`);

	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;

  	if (result.error) {
	    returnedData = result; 
	}
	else{
		let metadata = await readMetadataJSON();

		var newObject = {};
		newObject.data = [];
		newObject.error = false;

		let token = result.data.items[0];

		let contract = token.contract_address.toLowerCase();
		if(Object.keys(metadata).includes(contract)){
			token.logo_url = "resource/tokensInfo/logos/"+metadata[contract].logo;
		}
		else{
			token.logo_url = "resource/tokensInfo/logos/nologo__.png";
		}
		newObject.data.push(token);
		returnedData = newObject;
	}

  	return returnedData;
}


async function getTokenBalances(chainId, address, APIKEY) {

	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/`);
	url.search = new URLSearchParams({
	    key: APIKEY,
	    nft: false,
	    limit: 10000
	})

	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;
  	returnedData = result;

  	if (result.error) {
	    returnedData = result; 
	} else {
  		let metadata = await readMetadataJSON();

		var newObject = {};
		newObject.data = [];
		newObject.error = false;

		let tokens = result.data.items;

		for(var id in tokens){
			if(tokens[id].contract_address){
				
					let contract = tokens[id].contract_address.toLowerCase();
					if(Object.keys(metadata).includes(contract)){
						tokens[id].logo_url = "resource/tokensInfo/logos/"+metadata[contract].logo;
					}
					else{
						tokens[id].logo_url = "resource/tokensInfo/logos/nologo__.png";
					}
					newObject.data.push(tokens[id]);
				
			}
		}
		returnedData = newObject;
	}

  	return returnedData;
}

async function getSpecificTokenBalances(chainId, address, contract, APIKEY) {

	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/?key=${APIKEY}&primer=%5B%7B%22%24match%22%3A%7B%22contract_address%22%3A+%22${contract.toLowerCase()}%22%7D%7D%5D`);
  	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;

  	if (result.error) {
	    returnedData = 0; 
	} else {
		returnedData = result.data.items[0].balance / 10 ** result.data.items[0].contract_decimals; 
	}

  	return returnedData;
}


async function getNFTs(chainId, address, APIKEY) {

	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/balances_v2/`);
	url.search = new URLSearchParams({
	    key: APIKEY,
	    nft: true,
	    limit: 10000
	})


	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;
  	returnedData = result;

  	if (result.error) {
	    returnedData = result; 
	} else {
		var newObject = {};
		newObject.data = [];
		newObject.error = false;

		let tokens = result.data.items;

		for(var id in tokens){
			if(tokens[id].contract_address){
				if(tokens[id].balance > 0){
					if(tokens[id].type == "nft"){
						newObject.data.push(tokens[id]);
					}
				}
			}
		}
		returnedData = newObject;
	}

  	return returnedData;
}


async function getTokenTransfers(chainId, address, contract, APIKEY) {

	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/transfers_v2/`);
	url.search = new URLSearchParams({
		"page-size": 10000,
	    key: APIKEY,
	    "contract-address": contract,
	    limit: 10000
	})

	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;
  	returnedData = result;

  	if (result.error) {
	    console.log(new Error(`Error #${result.error_code}: ${result.error_message}`));
	    
	} 

	returnedData = result; 

  	return returnedData;
}


async function getFaucetClaims(chainId, address, APIKEY) {
	const url = new URL(`https://api.covalenthq.com/v1/${chainId}/address/${address}/transactions_v2/?page-size=100000&key=${APIKEY}`);

	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;

  	if (result.error) {
		returnedData = result; 
	} 
	else{
		var newObject = {};
		newObject.data = {};
		newObject.data.items = [];
		newObject.error = false;

		let claims = result.data.items;

		for(var id in claims){
			if(claims[id].from_address == "0x88250f772101179a4ecfaa4b92a983676a3ce445"){
				newObject.data.items.push(claims[id]);
			}
		}
		returnedData = newObject;
	}


  	return returnedData;
}




// Sovryn prices

async function getTokenPrices() {
	const url = new URL(`https://backend.sovryn.app/tvl`)

	let response = await fetch(url);
  	let result = await response.json();
  	let returnedData;

  	let newObject = {};
	newObject.tokens = [];

	let contracts = [];
	let contract_address;

	let metadata = await readMetadataJSON();

	for(var id in result.tvlAmm){
		if(result.tvlAmm[id].asset){
			contract_address = result.tvlAmm[id].asset.toLowerCase();

			if( !contracts.includes(contract_address) ){
				contracts.push(contract_address);
				if( Object.keys(metadata).includes(contract_address) ){
					result.tvlAmm[id].logo_url = "resource/tokensInfo/logos/"+metadata[contract_address].logo;
				}
				else{
					result.tvlAmm[id].logo_url = "resource/tokensInfo/logos/nologo__.png";
				}

				result.tvlAmm[id].price_USD = result.tvlAmm[id].balanceUsd / result.tvlAmm[id].balance;

				newObject.tokens.push(result.tvlAmm[id])
			}
		}
	}

	returnedData = newObject;

  	return returnedData;
}


// Sovryn TVL Bitocracy Staking Contract [tvlStaking]
async function tvlStaking(chainId, APIKEY) {
	
	let response = await fetch(`https://backend.sovryn.app/tvl`);
  	let result = await response.json();
  	let metadata = result.tvlStaking;

  	let returnedData = {
  		"balance": 0,
  		"balance_usd": 0
  	}
  	
  	for (var i in metadata) {
  		if(metadata[i].asset){
	  		metadata[i].price_USD = metadata[i].balanceUsd / metadata[i].balance;

	  		var asset_balance = await getSpecificTokenBalances(chainId, metadata[i].contract, metadata[i].asset, APIKEY);
	  		returnedData.balance += asset_balance;
	  		returnedData.balance_usd += asset_balance * metadata[i].price_USD;
  		}
  	}

  	return returnedData;
}

// Sovryn TVL Protocol Contract		 [tvlProtocol]
async function tvlProtocol(chainId, APIKEY) {
	
	let response = await fetch(`https://backend.sovryn.app/tvl`);
  	let result = await response.json();
  	let metadata = result.tvlProtocol;

  	let returnedData = {
  		"balance": 0,
  		"balance_usd": 0
  	}
  	
  	for (var i in metadata) {
  		if(metadata[i].asset){
	  		metadata[i].price_USD = metadata[i].balanceUsd / metadata[i].balance;

	  		var asset_balance = await getSpecificTokenBalances(chainId, metadata[i].contract, metadata[i].asset, APIKEY);
	  		if(asset_balance != 0){
	  			returnedData.balance += asset_balance;
	  			returnedData.balance_usd += asset_balance * metadata[i].price_USD;
	  		}
  		}
  	}

  	return returnedData;
}

// Sovryn TVL Lending Contract	 [tvlLending]
async function tvlLending(chainId, APIKEY) {
	
	let response = await fetch(`https://backend.sovryn.app/tvl`);
  	let result = await response.json();
  	let metadata = result.tvlLending;

  	let returnedData = {
  		"balance": 0,
  		"balance_usd": 0
  	}
  	
  	for (var i in metadata) {
  		if(metadata[i].asset){
	  		metadata[i].price_USD = metadata[i].balanceUsd / metadata[i].balance;

	  		var asset_balance = await getSpecificTokenBalances(chainId, metadata[i].contract, metadata[i].asset, APIKEY);
	  		if(asset_balance != 0){
	  			returnedData.balance += asset_balance;
	  			returnedData.balance_usd += asset_balance * metadata[i].price_USD;
	  		}
  		}
  	}

  	return returnedData;
}

// Sovryn TVL Amm Contract		 [tvlAmm]
async function tvlAmm(chainId, APIKEY) {
	
	let response = await fetch(`https://backend.sovryn.app/tvl`);
  	let result = await response.json();
  	let metadata = result.tvlAmm;

  	let returnedData = {
  		"balance": 0,
  		"balance_usd": 0
  	}
  	
  	for (var i in metadata) {
  		if(metadata[i].asset){
	  		metadata[i].price_USD = metadata[i].balanceUsd / metadata[i].balance;

	  		var asset_balance = await getSpecificTokenBalances(chainId, metadata[i].contract, metadata[i].asset, APIKEY);
	  		if(asset_balance != 0){
	  			returnedData.balance += asset_balance;
	  			returnedData.balance_usd += asset_balance * metadata[i].price_USD;
	  		}
  		}
  	}

  	return returnedData;
}