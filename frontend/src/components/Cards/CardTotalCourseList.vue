<template>

	<!-- Invoices Card -->
	<a-card :bordered="false" class="header-solid h-full" :bodyStyle="{paddingTop: 0, paddingBottom: '16px' }">
		<template #title>
			<h6 class="font-semibold m-0">{{ title }}</h6>
		</template>
		
		<a-list
			class="course-list"
			item-layout="horizontal"
			:split="false"
			:data-source="data"
		>
            
			<a-list-item slot="renderItem" slot-scope="item">
                <a slot="actions">
                    {{ item.male }}
                </a>
                <a slot="actions">
                    {{ item.female }}
                </a>
				<a-list-item-meta
					:title="item.title"
				></a-list-item-meta>
			</a-list-item>

            
            <a-list-item slot="header">
                <a slot="actions">
                    Sum of Male
                </a>
                <a slot="actions">
                    Sum of Female
                </a>
				<a-list-item-meta
					title="Row Labels"
				></a-list-item-meta>
			</a-list-item>
            <a-list-item slot="footer">
                <a slot="actions">
                    {{ totalMaleCount }}
                </a>
                <a slot="actions">
                    {{ totalFemaleCount }}
                </a>
				<a-list-item-meta
					title="Grand Total"
				></a-list-item-meta>
			</a-list-item>
		</a-list>
	</a-card>
	<!-- / Invoices Card -->

</template>

<script>

	export default ({
		props: {
			data: {
				type: Array,
				default: () => [],
			},
            title: {
                type: String,
                default: 'Summary'
            }
		},
		data() {
			return {
			}
		},
        computed: {
            totalMaleCount(){
                console.log(this.data)
				let maleCount = this.data.reduce((a, b) => Number(a) + Number(b.male), 0)
				return maleCount
			},
			totalFemaleCount(){
				let femaleCount = this.data.reduce((a, b) => Number(a) + Number(b.female), 0)
				return femaleCount
			},
        },
		methods:{
			async downloadFormat(file, templateName){
				const anchor = document.createElement('a');
				anchor.href = file;
				anchor.target = '_blank';
				anchor.download = templateName;
				anchor.click();
			},
		}
	})

</script>